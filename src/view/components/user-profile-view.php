<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class UserProfileView extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');

    return HtmlElement::emmetTop('a#login-anchor.login', [
      'href' => $urlQuery->assign([
        'page' => 'login',
        'previous-page' => $this->get('page'),
      ])->getUrlQuery(),
      new PrimaryButton('Đăng nhập'),
    ]);
  }
}
?>
