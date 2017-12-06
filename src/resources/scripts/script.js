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

function renderTemplate(template, data = {}, clone = false) {
  if (typeof template === 'string') {
    return renderTemplate(document.querySelector(template), data)
  }

  const result = template.content.cloneNode(true)

  const getNode = clone
    ? node => node.cloneNode(true)
    : x => x

  for (const selector in data) {
    const node = getNode(createDOMNode(data[selector]))

    Array
      .from(result.querySelectorAll(selector))
      .forEach(container => container.appendChild(node))
  }

  return result
}

Object.assign(renderTemplate, {
  transform: (fn = (k, v) => [k, v], template, data = {}, clone = false) => {
    const newData = {}

    for (const key in data) {
      const [newKey, newValue] = fn(key, data[key], data)
      newData[newKey] = newValue
    }

    return renderTemplate(template, newData, clone)
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

; (function ({document}) {
  const profileButton = document.getElementById('profile-button')
  const profileSetting = document.getElementById('profile-setting')
  const navHidingButton = document.getElementById('nav-hiding-button')
  const player = document.querySelector('.x-component--player')
  const isLoggedIn = document.documentElement.classList.contains('logged-in')

  const isFavourite = () => player.classList.contains('favourite')
  const addFavourite = () => player.classList.add('favourite')
  const removeFavourite = () => player.classList.remove('favourite')

  profileButton && profileButton.addEventListener('click', () => {
    profileSetting.hidden = !profileSetting.hidden
  }, false)

  navHidingButton && navHidingButton.addEventListener('click', () => {
    const {classList} = document.documentElement
    if (classList.contains('nav-hidden')) {
      classList.remove('nav-hidden')
    } else {
      classList.add('nav-hidden')
    }
  })

  ; (function (container) {
    if (!container) return

    if (isLoggedIn) {
      const toggleFavButton = document.createElement('button')
      toggleFavButton.classList.add('toggle-favourite')
      container.appendChild(toggleFavButton)

      toggleFavButton.addEventListener('click', async function ajaxFav () {
        const key = isFavourite() ? 'userDeleteFavourite' : 'userAddFavourite'
        isFavourite() ? removeFavourite() : addFavourite()
        const response = await ajax({[key]: player.dataset.gameId})
        response.payload[key] ? addFavourite() : removeFavourite()
      }, false)
    }
  })(document.querySelector('.x-component--player .control'))
})(window)
