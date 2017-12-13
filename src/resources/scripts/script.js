'use strict'

; (function ({document, location}) {
  const isLoggedIn = document.documentElement.classList.contains('logged-in')
  const isAdminPage = /\?page=admin/.test(location.href)
  const loadJsonData = createJsonEmbedLoader()

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

    callIfExists.querySelector('comment-thread-container', container => {
      const getKnownComments = container => Array
        .from(container.querySelectorAll('.x-component--comment-viewer'))
        .map(element => parseInt(element.dataset.id))

      const sendReplyingComment = (target, container, reply) => ajax({
        userDiffReplyingComment: {
          [target]: {
            knownComments: getKnownComments(container),
            reply
          }
        }
      })

      Array
        .from(container
          .querySelectorAll('.x-component--comment-thread-viewer')
        )
        .forEach(thread => {
          const targetedCommentId = parseInt(thread
            .querySelector('surface-comment-container .x-component--comment-viewer')
            .dataset
            .id
          )

          const replyingCommentContainer = thread.querySelector('replying-comment-container')

          const createReplyingCommentButton = comment => {
            renderTemplate.byClass(
              '#replying-comment-button',
              {
                outer: {
                  dataset: {
                    targetedCommentId
                  },
                  events: {click () {
                    callIfExists(thread.querySelector('comment-editor'), x => x.remove())

                    const onSubmit = () => {
                      const {value} = editor.querySelector('textarea')
                      editor.remove()

                      const {fullname, username} = loadJsonData('user-info')
                      const newComment = renderTemplate.byClass(
                        '#comment-viewer',
                        {
                          fullname,
                          username,
                          content: value
                        },
                        false,
                        replyingCommentContainer
                      )

                      createReplyingCommentButton(newComment)
                      newComment.focus()

                      sendReplyingComment(targetedCommentId, thread, value).catch(error => {
                        newComment.remove()
                        console.warn(error)
                      })
                    }

                    const onCancel = () => editor.remove()

                    const editor = renderTemplate.byClass(
                      '#comment-editor',
                      {
                        submit: {events: {click: onSubmit}},
                        cancel: {events: {click: onCancel}},
                        editor: {events: {
                          keydown: event => {
                            if (event.shiftKey) return

                            switch (event.keyCode) {
                              case 13: // ENTER
                                event.preventDefault()
                                onSubmit()
                                break
                              case 27: // ESC
                                event.preventDefault()
                                onCancel()
                                break
                            }
                          }
                        }}
                      },
                      false,
                      replyingCommentContainer
                    )

                    setTimeout(() => editor.querySelector('textarea').focus())
                  }}
                }
              },
              false,
              comment.querySelector('comment-text')
            )
          }

          Array
            .from(thread.querySelectorAll('.x-component--comment-viewer'))
            .forEach(createReplyingCommentButton)
        })
    })
  }
})(window)
