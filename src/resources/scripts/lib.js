'use strict'

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
    const node = getNode(createDOMNode(data[selector]))

    Array
      .from(fragment.querySelectorAll(selector))
      .forEach(container => container.appendChild(node))
  }

  const result = fragment.querySelector('*')
  target instanceof Node && target.appendChild(result)
  return result
}

Object.assign(renderTemplate, {
  transform: (fn = (k, v) => [k, v], template, data = {}, ...args) => {
    const newData = {}

    for (const key in data) {
      const [newKey, newValue] = fn(key, data[key], data)
      newData[newKey] = newValue
    }

    return renderTemplate(template, newData, ...args)
  },

  transformKey: (fn = x => x, ...args) =>
    renderTemplate.transform((k, v) => [fn(k), v], ...args),

  transformValue: (fn = x => x, ...args) =>
    renderTemplate.transform((k, v) => [k, fn(v)], ...args),

  prefix: (prefix = '', ...args) =>
    renderTemplate.transformKey(x => prefix + x, ...args),

  suffix: (suffix = '', ...args) =>
    renderTemplate.transformKey(x => x + suffix, ...args),

  byClass: (...args) =>
    renderTemplate.prefix('.', ...args),

  byId: (...args) =>
    renderTemplate.prefix('#', ...args),

  __proto__: null
})

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

  const onClick = event => {
    const {target} = event
    event.stopPropagation()

    if (target === toggler || toggler.contains(target)) {
      check() ? remove() : add()
    } else {
      remove()
    }
  }

  document.addEventListener('click', onClick, false)

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
      document,
      __proto__: null
    },
    class: {
      name,
      __proto__: null
    },
    __proto__: null
  }
}
