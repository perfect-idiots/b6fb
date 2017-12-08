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
    xhr.addEventListener('loadend', () => resolve(JSON.parse(xhr.response)))
    xhr.addEventListener('error', error => reject(error))
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

  children: (container, children) => Array
    .from(children)
    .map(createDOMNode)
    .forEach(child => container.appendChild(child)),

  textContent: (container, text) => {
    container.textContent = text
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
