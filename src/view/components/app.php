<?php
require_once __DIR__ . '/base.php';

class App implements Component {
  public function render(): Component {
    return HtmlElement::create('html', array(
      'lang' => 'en',
      HtmlElement::create('head', array(
        HtmlElement::create('meta', array('charset' => 'utf-8')),
        HtmlElement::create('title', 'Hello, World!!')
      )),
      HtmlElement::create('body', array(
        HtmlElement::create('header'),
        HtmlElement::create('main'),
        HtmlElement::create('footer')
      ))
    ));
  }
}
?>
