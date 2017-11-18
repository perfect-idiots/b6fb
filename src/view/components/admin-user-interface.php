<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/../../lib/utils.php';

class AdminUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('html', [
      'lang' => 'en',
      'classes' => ['admin'],
      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', 'Administration'),
        CssView::fromFile(__DIR__ . '/../../resources/admin.css'),
      ]),
      HtmlElement::emmetBottom('body>#page.aligner', [
        HtmlElement::emmetTop('.top-aligned.aligned-item', []),
        HtmlElement::emmetTop('.middle-aligned.aligned-item', [
          HtmlElement::create('header', Logo::instance($this->getData())),
          HtmlElement::emmetBottom('section#main-section', [
            HtmlElement::emmetTop('h1#login-title', 'Đăng nhập'),
            HtmlElement::create('main', [
              new LoginForm([
                'hidden-values' => [
                  'page' => 'admin',
                ],
              ]),
            ]),
          ]),
        ]),
        HtmlElement::emmetTop('.bottom-aligned.aligned-item', []),
      ]),
    ]);
  }
}
?>
