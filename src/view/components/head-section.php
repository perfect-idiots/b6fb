<?php
require_once __DIR__ . '/base.php';

class HeadSection implements Component {
  public function render(): Component {
    return HtmlElement::create('head', array(
      HtmlElement::create('meta', array('charset' => 'utf-8')),
      HtmlElement::create('title', array(
        'Hello, World!!'
      ))
    ));
  }
}
?>
