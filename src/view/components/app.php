<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/header-section.php';

class App implements Component {
  public function render(): Component {
    return HtmlElement::create('html', array(
      'lang' => 'en',
      HtmlElement::create('head', array(
        HtmlElement::create('meta', array('charset' => 'utf-8')),
        HtmlElement::create('title', 'Hello, World!!'),
        CssView::fromFile(__DIR__ . '/../../resources/style.css'),
      )),
      HtmlElement::create('body', array(
        new HeaderSection(),
        HtmlElement::create('main'),
        HtmlElement::create('footer'),
      ))
    ));
  }
}
?>
