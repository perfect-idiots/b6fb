<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class UserProfileView extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $login = $this->get('login');
    $images = $this->get('images');

    [$fullname, $username] = $login->isLoggedIn()
      ? $this->get('user-profile')->info()
      : ['', '']
    ;

    return $login->isLoggedIn()
      ? HtmlElement::emmetTop('#user-profile-view.logged-in', [
        HtmlElement::emmetBottom('button#profile-button>img#profile-image', [
          'src' => $images['default-avatar-white-image'],
        ]),
        HtmlElement::emmetTop('#profile-setting.popup', [
          'hidden' => true,
          HtmlElement::emmetTop('#popup-profile-view', [
            HtmlElement::emmetTop('img#popup-profile-image', [
              'src' => $images['default-avatar-white-image'],
            ]),
            HtmlElement::emmetBottom('a#popup-profile-identity', [
              'href' => $urlQuery->set('page', 'profile')->getUrlQuery(),
              HtmlElement::emmetTop('#popup-fullname', $fullname),
              HtmlElement::emmetTop('#popup-username', '@' . $username),
            ]),
          ]),
          HtmlElement::emmetTop('.button-container', [
            HtmlElement::emmetBottom('button.logout>a', [
              'href' => $urlQuery->assign([
                'page' => 'logout',
                'previous-page' => $this->get('page'),
              ])->getUrlQuery(),
              'Đăng xuất',
            ]),
          ]),
        ]),
      ])
      : HtmlElement::emmetTop('a#login-anchor.login', [
        'href' => $urlQuery->assign([
          'page' => 'login',
          'previous-page' => $this->get('page'),
        ])->getUrlQuery(),
        new PrimaryButton('Đăng nhập'),
      ])
    ;
  }
}
?>
