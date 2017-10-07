<?php
require_once __DIR__ . '/base.php';

class Body implements Component {
  public function render(): Component {
    return new Element('body', array(), array(
      new Element('h1', array(), array(
        new TextNode('Hello, World!!')
      ))
    ));
  }
}
?>
