'use strict'

; (function ({document}) {
  const profileButton = document.getElementById('profile-button')
  const profileSetting = document.getElementById('profile-setting')

  profileButton && profileButton.addEventListener('click', () => {
    profileSetting.hidden = !profileSetting.hidden
  }, false)
})(window)
