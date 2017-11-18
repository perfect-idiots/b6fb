<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/login-form.php';
require_once __DIR__ . '/../../lib/utils.php';

class LoginUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();
    $cssVars = array_merge($data['colors'], $data['sizes'], $data['images']);
    $urlQuery = $this->get('url-query');
    $prevPage = $urlQuery->getDefault('previous-page', 'index');

    return HtmlElement::emmetTop('html.login.user-account', [
      'lang' => 'en',
      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', 'Đăng nhập'),
        new CssView(__DIR__ . '/../../resources/style.css', $cssVars),
        new CssView(__DIR__ . '/../../resources/login.css'),
      ]),
      HtmlElement::create('body', [
        HtmlElement::emmetBottom('header>h1', 'Đăng nhập'),
        HtmlElement::create('main', [
          new LoginForm($this->assign([
            'action' => '.',
            'sign-up' => $urlQuery->set('sign-up', 'sign-up')->getUrlQuery(),
          ])->getData()),
          HtmlElement::emmetBottom('button#back>a', [
            'href' => $urlQuery->except('previous-page')->set('page', $prevPage)->getUrlQuery(),
            'Quay lại',
          ]),
        ]),
        HtmlElement::create('footer'),
      ]),
    ]);
  }
}
?>
