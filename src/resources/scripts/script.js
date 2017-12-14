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
    const {fullname, username} = loadJsonData('user-info')

    const createSubmitCancelTemplate = (onSubmit, onCancel) => ({
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
    })

    const getKnownComments = container => Array
      .from(container.querySelectorAll('.x-component--comment-viewer'))
      .map(element => parseInt(element.dataset.id))

    const createReplyingCommentButton = (thread, comment) => {
      const replyingCommentContainer = thread.querySelector('replying-comment-container')

      const targetedCommentId = parseInt(thread
        .querySelector('surface-comment-container .x-component--comment-viewer')
        .dataset
        .id
      )

      const sendReplyingComment = (target, container, reply) => ajax({
        userDiffReplyingComment: {
          [target]: {
            knownComments: getKnownComments(container),
            reply
          }
        }
      })

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

                createReplyingCommentButton(thread, newComment)
                focusAndScroll(newComment)

                sendReplyingComment(targetedCommentId, thread, value).catch(error => {
                  newComment.remove()
                  console.warn(error)
                })
              }

              const onCancel = () => editor.remove()

              const editor = renderTemplate.byClass(
                '#comment-editor',
                createSubmitCancelTemplate(onSubmit, onCancel),
                false,
                replyingCommentContainer
              )

              setTimeout(() => focusAndScroll(editor.querySelector('textarea')))
            }}
          }
        },
        false,
        comment.querySelector('comment-text')
      )
    }

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
      const sendSurfaceComment = (knownComments, gameId, content) => ajax({
        userDiffSurfaceComments: {
          knownComments,
          byGame: {
            [gameId]: [content]
          }
        }
      })

      const onSubmit = () => {
        const knownComments = getKnownComments(document)
        const threadContainer = document.querySelector('comment-thread-container > article')
        const {value} = textarea
        textarea.value = ''

        const earlyCommentThread = renderTemplate.byClass(
          '#comment-thread-viewer',
          {
            username,
            fullname,
            content: value
          },
          false,
          threadContainer
        )

        focusAndScroll(earlyCommentThread)

        const {gameId} = loadJsonData('url-query')
        sendSurfaceComment(knownComments, gameId, value).then(response => {
          const {byGame} = response.payload.userDiffSurfaceComments
          earlyCommentThread.remove()
          if (!byGame) return

          const {groups} = byGame[gameId]
          for (const threadInfo of groups) {
            const {top} = threadInfo

            const thread = renderTemplate.byClass(
              '#comment-thread-viewer',
              {
                comment: {
                  dataset: {
                    id: top.id,
                    parent: top['parent-comment-id'] || ''
                  },
                },
                fullname: top['author-fullname'],
                username: top['author-id'],
                content: top.content
              },
              false,
              threadContainer
            )

            focusAndScroll(thread)
            createReplyingCommentButton(
              thread,
              thread.querySelector('.x-component--comment-viewer')
            )

            const replyingCommentContainer = thread.querySelector('replying-comment-container')
            for (const replyInfo of threadInfo.replies) {
              const reply = renderTemplate.byClass(
                '#comment-viewer',
                {
                  comment: {
                    dataset: {
                      id: replyInfo.id,
                      parent: replyInfo['parent-comment-id'] || ''
                    }
                  },
                  fullname: replyInfo['author-fullname'],
                  username: replyInfo['author-id'],
                  content: reply.content
                },
                false,
                thread.querySelector('replying-comment-container')
              )

              createReplyingCommentButton(thread, reply)
            }
          }
        }).catch(error => {
          earlyCommentThread.remove()
          console.warn(error)
        })
      }

      const onCancel = () => {
        textarea.value = ''
      }

      const editor = renderTemplate.byClass(
        '#comment-editor',
        createSubmitCancelTemplate(onSubmit, onCancel),
        false,
        container,
      )

      const textarea = editor.querySelector('textarea')
    })

    callIfExists.querySelector('comment-thread-container', container => {
      Array
        .from(container
          .querySelectorAll('.x-component--comment-thread-viewer')
        )
        .forEach(thread => Array
          .from(thread.querySelectorAll('.x-component--comment-viewer'))
          .forEach(comment => createReplyingCommentButton(thread, comment))
        )
    })
  }
})(window)
