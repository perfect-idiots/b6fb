<?php
require_once __DIR__ . '/base.php';

class Body implements Component {
  public function render(): Component {
    return new HTMLElement('body', array(), array(
      new HTMLElement('h1', array(), array(
        new TextNode('Hello, World!!')
      ))
    ));
  }
}
?>
