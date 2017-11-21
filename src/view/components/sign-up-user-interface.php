<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/sign-up-form.php';
require_once __DIR__ . '/../../lib/utils.php';

class SignUpUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::emmetTop('html#sign-up', [
      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', 'Tạo tài khoản'),
        CssView::fromFile(__DIR__ . '/../../resources/sign-up.css'),
      ]),
      HtmlElement::emmetBottom('body>#page', [
        HtmlElement::emmetBottom('header#main-header', [
          new Logo($this->getData()),
        ]),
        HtmlElement::emmetBottom('section#main-section>main', [
          HtmlElement::emmetTop('h2#subtitle', 'Tạo tài khoản của bạn'),
          SignUpForm::instance($this->getData()),
        ]),
        HtmlElement::create('footer'),
      ]),
    ]);
  }
}
?>
