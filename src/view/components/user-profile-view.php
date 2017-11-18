<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class UserProfileView extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $login = $this->get('login');
    $images = $this->get('images');

    return $login->isLoggedIn()
      ? HtmlElement::emmetTop('#user-profile-view.logged-in', [
        HtmlElement::emmetBottom('button#profile-button>img#profile-image', [
          'src' => $images['default-avatar-image'],
        ]),
        HtmlElement::emmetTop('#profile-setting', [
          'hidden' => true,
          HtmlElement::emmetBottom('button.logout>a', [
            'href' => $urlQuery->assign([
              'page' => 'logout',
              'previous-page' => $this->get('page'),
            ])->getUrlQuery(),
            'Đăng xuất',
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
