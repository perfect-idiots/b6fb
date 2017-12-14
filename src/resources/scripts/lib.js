'use strict'

const hello = 'world'

window.convertCase = (function () {
  const REGEX = {
    WORD_DELIM: /[_\- \t\n\r]+/,
    NEWLINE: /\r?\n\r?/
  }

  const convertCase = {
    toCamelCase: (string = '') => {
      const [first, ...rest] = String(string).split(REGEX.WORD_DELIM)

      return [
        first.toLowerCase(),
        ...rest.map(convertCase.capitalize)
      ].join('')
    },

    toPascalCase: (string = '') =>
      String(string).split(REGEX.WORD_DELIM).map(convertCase.capitalize).join(''),

    toKebabCase: (string = '') =>
      convertCase.seperaterize(string, convertCase.lowerize, '-'),

    toUpperKebabCase: (string = '') =>
      convertCase.seperaterize(string, convertCase.upperize, '-'),

    toSnakeCase: (string = '') =>
      convertCase.seperaterize(string, convertCase.lowerize, '_'),

    toUpperSnakeCase: (string = '') =>
      convertCase.seperaterize(string, convertCase.upperize, '_'),

    seperaterize: (string = '', convertCase, delim) =>
      String(string).split(REGEX.WORD_DELIM).map(convertCase).join(delim),

    lowerize: (string = '') => String(string).toLowerCase(),

    upperize: (string = '') => String(string).toUpperCase(),

    capitalize: ([first, ...rest] = '') => [
      String(first).toUpperCase(),
      ...rest.map(x => String(x).toLowerCase())
    ].join('')
  }

  return convertCase
})()

function ajax (query) {
  const xhr = new XMLHttpRequest()
  xhr.open('POST', '?type=api')

  return new Promise((resolve, reject) => {
    xhr.addEventListener('loadend', () => {
      const {status, response} = xhr

      if (status < 200 || status > 200) {
        reject({status, response, xhr})
      } else {
        try {
          resolve(JSON.parse(response))
        } catch (error) {
          reject({error, status, response, xhr})
        }
      }
    })

    xhr.addEventListener('error', error => reject({error, __proto__: xhr}))
    xhr.send(JSON.stringify(query))
  })
}

function renderTemplate(template, data = {}, clone = false, target = null) {
  if (typeof template === 'string') {
    return renderTemplate(document.querySelector(template), data, clone, target)
  }

  const fragment = template.content.cloneNode(true)

  const getNode = clone
    ? node => node.cloneNode(true)
    : x => x

  for (const selector in data) {
    const append = renderTemplate.createAppendFunction(data[selector])

    Array
      .from(fragment.querySelectorAll(selector))
      .forEach(append)
  }

  const result = fragment.querySelector('*')
  target instanceof Node && target.appendChild(result)
  return result
}

renderTemplate.createAppendFunction = prototype => {
  try {
    const node = createDOMNode(prototype)
    return container => container.appendChild(node)
  } catch (_) {
    return container => {
      for (const key in prototype) {
        const value = prototype[key]
        const append = renderTemplate.createAppendFunction.attributes[key]
        append ? append(container, value) : container.setAttribute(key, value)
      }
    }
  }
}

renderTemplate.createAppendFunction.attributes = {
  '': (container, child) =>
    container.appendChild(createDOMNode(child)),

  classList: (container, classes) =>
    container.classList.add(...classes),

  dataset: (container, dataset) =>
    Object.assign(container.dataset, dataset),

  children: (container, children) => Array
    .from(children)
    .map(createDOMNode)
    .forEach(child => container.appendChild(child)),

  textContent: (container, text) => {
    container.textContent = text
  },

  events: (container, listeners) => {
    if (typeof listeners !== 'object') {
      throw new TypeError(`Second argument is not an object: ${listeners}`)
    }

    const add = (type, fn) => fn instanceof Array
      ? fn.forEach(x => add(type, x))
      : container.addEventListener(type, fn, false)

    for (const type in listeners) {
      add(type, listeners[type])
    }
  },

  __proto__: null
}

renderTemplate.transform = (fn = (k, v) => [k, v], template, data = {}, ...args) => {
  const newData = {}

  for (const key in data) {
    const [newKey, newValue] = fn(key, data[key], data)
    newData[newKey] = newValue
  }

  return renderTemplate(template, newData, ...args)
}

renderTemplate.transformKey = (fn = x => x, ...args) =>
  renderTemplate.transform((k, v) => [fn(k), v], ...args)

renderTemplate.transformValue = (fn = x => x, ...args) =>
  renderTemplate.transform((k, v) => [k, fn(v)], ...args)

renderTemplate.prefix = (prefix = '', ...args) =>
  renderTemplate.transformKey(x => prefix + x, ...args)

renderTemplate.suffix = (suffix = '', ...args) =>
  renderTemplate.transformKey(x => x + suffix, ...args)

renderTemplate.byClass = (...args) =>
  renderTemplate.prefix('.', ...args)

renderTemplate.byId = (...args) =>
  renderTemplate.prefix('#', ...args)

function createDOMNode (content) {
  if (content instanceof Node) return content

  if (['string', 'number'].includes(typeof content)) {
    return document.createTextNode(String(content))
  }

  if (content instanceof Array) {
    const result = document.createDocumentFragment()
    Array.from(content).map(createDOMNode).forEach(child => result.appendChild(child))
    return result
  }

  throw new TypeError(`Invalid type of content: ${content}`)
}

function isFlashSupported () {
  let result

  try {
    result = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
  } catch (_) {
    result = navigator.plugins['Shockwave Flash']
  }

  return Boolean(result)
}

function callIfExists (subject, ontrue = x => x, onfalse = x => x) {
  return (subject ? ontrue : onfalse)(subject, ontrue, onfalse)
}

callIfExists.querySelector = (x = 'html', ...args) =>
  callIfExists(document.querySelector(x), ...args)

function makeClassToggler (toggler, target, name) {
  if (typeof toggler === 'string') {
    toggler = document.querySelector(toggler)
  }

  if (typeof target === 'string') {
    target = document.querySelector(target)
  }

  const {classList} = target
  const check = () => classList.contains(name)
  const add = () => classList.add(name)
  const remove = () => classList.remove(name)
  const onClick = event => check() ? remove() : add()

  toggler.addEventListener('click', onClick, false)

  return {
    fn: {
      check,
      add,
      remove,
      onClick,
      __proto__: null
    },
    node: {
      toggler,
      target,
      __proto__: null
    },
    class: {
      name,
      __proto__: null
    },
    __proto__: null
  }
}

function eventElsewhere (type = 'click', fn = () => {}, ...here) {
  document.addEventListener(type, event => {
    const {target} = event
    here.some(x => x.contains(target)) || fn()
  }, false)
}

function createJsonEmbedLoader () {
  const loaded = Object.create(null)

  return id => {
    if (id in loaded) return loaded[id]
    const selector = `script#data-${id}.x-component--json-data-embed`
    const element = document.querySelector(selector)

    const transform = x => {
      if (!x || typeof x !== 'object') return x
      if (x instanceof Array) return x.map(transform)

      const result = {}
      for (const key in x) {
        result[convertCase.toCamelCase(key)] = transform(x[key])
      }

      return result
    }

    const value = transform(JSON.parse(element.text))
    loaded[id] = value
    return value
  }
}

function createSizeTracker (element, delay = 1024) {
  if (typeof element === 'string') {
    element = document.querySelector(element)
  }

  let rect = element.getBoundingClientRect()
  const listeners = {width: [], height: [], all: []}

  const check = newRect =>
    ['width', 'height'].filter(x => rect[x] !== newRect[x])

  const call = (name, newVal, oldVal, newRect = newVal, oldRect = oldVal) => {
    listeners[name].forEach(fn => setTimeout(
      () => fn({newVal, oldVal, newRect, oldRect, element})
    ))
  }

  const loop = () => {
    const newRect = element.getBoundingClientRect()
    const change = check(newRect)
    if (!change.length) return
    change.includes('width') && call('width', newRect.width, rect.width, newRect, rect)
    change.includes('height') && call('height', newRect.height, rect.height, newRect, rect)
    call('all', newRect, rect)
    rect = newRect
  }

  const intervalId = setInterval(loop, delay)
  window.addEventListener('resize', loop, false)

  const validate = fn => {
    if (typeof fn !== 'function') {
      throw new TypeError(`Invalid type of callback: ${fn}`)
    }
  }

  const createListenerAdder = key => fn => {
    validate(fn)
    listeners[key].push(fn)
    return result
  }

  const proto = {
    element,
    delay,
    intervalId,
    __proto__: null
  }

  const result = {
    validate,
    stop: () => {
      clearInterval(intervalId)
      window.removeEventListener('resize', loop, false)
    },
    onChange: createListenerAdder('all'),
    width: {
      onChange: createListenerAdder('width'),
      __proto__: proto
    },
    height: {
      onChange: createListenerAdder('height'),
      __proto__: proto
    },
    __proto__: proto
  }

  element.addEventListener('DOMNodeRemoved', result.stop, false)

  return result
}

function focusAndScroll (element) {
  element.focus()
  element.scrollIntoView()
}
