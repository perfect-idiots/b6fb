'use strict'

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
