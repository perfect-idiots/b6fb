<?php
require_once __DIR__ . '/base.php';

class Head implements Component {
  public function render(): Component {
    return new Element('head', array(), array(
      new Element('meta', array(
        'attributes' => array('charset' => 'utf-8')
      )),
      new Element('title', array(), array(
        new TextNode('Hello, World!!')
      ))
    ));
  }
}
?>
