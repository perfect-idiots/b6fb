<?php
require_once __DIR__ . '/base.php';

class Head implements Component {
  public function render(): Component {
    return new HTMLElement('head', array(), array(
      new HTMLElement('meta', array(
        'attributes' => array('charset' => 'utf-8')
      )),
      new HTMLElement('title', array(), array(
        new TextNode('Hello, World!!')
      ))
    ));
  }
}
?>
