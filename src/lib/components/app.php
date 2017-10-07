<?php
require_once __DIR__ . '/base.php';

class App implements Component {
  public function render(): Component {
    return new Element('html', array(), array(
      new Element('head', array(), array(
        new Element('meta', array(
          'attributes' => array('charset' => 'utf-8')
        )),
        new Element('title', array(), array(
          new TextNode('Hello, World!!')
        ))
      )),

      new Element('body', array(), array(
        new Element('h1', array(), array(
          new TextNode('Hello, World!!')
        ))
      ))
    ));
  }
}
?>
