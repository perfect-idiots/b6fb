'use strict'

; (function ({document, location}) {
  const isLoggedIn = document.documentElement.classList.contains('logged-in')
  const isAdminPage = /\?page=admin/.test(location.href)

  callIfExists.querySelector('#profile-button', button => {
    const profileSetting = document.getElementById('profile-setting')

    button.addEventListener('click', () => {
      profileSetting.hidden = !profileSetting.hidden
    }, false)

    eventElsewhere('click', () => {
      profileSetting.hidden = true
    }, button, profileSetting)
  })

  callIfExists.querySelector('#nav-hiding-button', button => {
    makeClassToggler(button, document.documentElement, 'nav-hidden')
  })

  if (isLoggedIn && !isAdminPage) {
    const player = document.querySelector('.x-component--player')
    const isFavourite = () => player.classList.contains('favourite')
    const addFavourite = () => player.classList.add('favourite')
    const removeFavourite = () => player.classList.remove('favourite')

    callIfExists.querySelector('.x-component--player .control', container => {
      let lock = false

      renderTemplate(
        '#toggle-favourite-button',
        {},
        false,
        container
      ).addEventListener('click', async function ajaxFav ({target}) {
        if (lock) return
        lock = true
        target.disabled = true
        target.style.cursor = 'wait'

        const key = isFavourite() ? 'userDeleteFavourite' : 'userAddFavourite'
        isFavourite() ? removeFavourite() : addFavourite()
        const response = await ajax({[key]: player.dataset.gameId})
        response.payload[key] ? addFavourite() : removeFavourite()

        lock = false
        target.disabled = false
        target.style.cursor = 'pointer'
      }, false)
    })

    callIfExists.querySelector('comment-editor-container', container => {
      const editor = renderTemplate(
        '#comment-editor',
        {},
        false,
        container,
      )

      const textarea = editor.querySelector('textarea')

      const resizeTextArea = () => {
        const editorSize = editor
          .getBoundingClientRect()
          .width

        const avatarSize = editor
          .querySelector('comment-image')
          .getBoundingClientRect()
          .width

        textarea.style.width = `${editorSize - avatarSize - 20}px`
      }

      resizeTextArea()
      createSizeTracker(editor, 0).width.onChange(resizeTextArea)
    })
  }
})(window)
