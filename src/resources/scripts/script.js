'use strict'

; (function ({document, location}) {
  const isLoggedIn = document.documentElement.classList.contains('logged-in')
  const isAdminPage = /\?page=admin/.test(location.href)

  callIfExists.querySelector('#profile-button', button => {
    const profileSetting = document.getElementById('profile-setting')

    button.addEventListener('click', () => {
      profileSetting.hidden = !profileSetting.hidden
    }, false)
  })

  callIfExists.querySelector('#nav-hiding-button', button => {
    const {classList} = document.documentElement

    button.addEventListener('click', () => {
      if (classList.contains('nav-hidden')) {
        classList.remove('nav-hidden')
      } else {
        classList.add('nav-hidden')
      }
    })
  })

  if (!isAdminPage) {
    const player = document.querySelector('.x-component--player')
    const isFavourite = () => player.classList.contains('favourite')
    const addFavourite = () => player.classList.add('favourite')
    const removeFavourite = () => player.classList.remove('favourite')

    callIfExists.querySelector('.x-component--player .control', container => {
      renderTemplate(
        '#toggle-favourite-button',
        {},
        false,
        container
      ).addEventListener('click', async function ajaxFav () {
        const key = isFavourite() ? 'userDeleteFavourite' : 'userAddFavourite'
        isFavourite() ? removeFavourite() : addFavourite()
        const response = await ajax({[key]: player.dataset.gameId})
        response.payload[key] ? addFavourite() : removeFavourite()
      }, false)
    })
  }
})(window)
