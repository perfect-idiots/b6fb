<?php
require_once __DIR__ . '/base.php';

class FooterSection implements Component {
  public function render(): Component {
    return HtmlElement::create('footer', [
      'id' => 'main-footer',
      HtmlElement::emmetTop('.copyright', 'Bản quyền © 2017 b6fb dev'),
      HtmlElement::emmetBottom('.about>a', [
        'href' => 'https://github.com/perfect-idiots',
        'Về chúng tôi',
      ]),
      HtmlElement::emmetBottom('.about>a', [
        'href' => 'https://github.com/perfect-idiots',
        'Liên hệ',
      ]),
    ]);
  }
}
?>
