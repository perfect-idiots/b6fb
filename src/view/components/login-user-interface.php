<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/login-form.php';
require_once __DIR__ . '/../../lib/utils.php';

class LoginUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $prevPage = $urlQuery->getDefault('previous-page', 'index');

    return HtmlElement::emmetTop('html.login.user-account', [
      'lang' => 'en',
      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', 'Đăng nhập'),
        CssView::fromFile(__DIR__ . '/../../resources/styles/login.css'),
      ]),
      HtmlElement::emmetBottom('body#login-page>#page.aligner', [
        HtmlElement::emmetTop('.top-aligned.aligned-item', []),
        HtmlElement::emmetTop('.middle-aligned.aligned-item', [
          HtmlElement::create('header', Logo::instance($this->getData())),
          HtmlElement::emmetBottom('section#main-section', [
            HtmlElement::emmetTop('h1#login-title', 'Đăng nhập'),
            HtmlElement::create('main', [
              new LoginForm($this->assign([
                'action' => '.',
                'sign-up' => $urlQuery->set('page', 'sign-up')->getUrlQuery(),
              ])->getData()),
            ]),
          ]),
        ]),
        HtmlElement::emmetTop('.bottom-aligned.aligned-item', []),
        HtmlElement::create('footer'),
      ]),
    ]);
  }
}
?>
