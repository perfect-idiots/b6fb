<?php
require_once __DIR__ . '/base.php';

class Body implements Component {
  public function render(): Component {
    return HTMLElement::create('body', array(
      HTMLElement::create('h1', array(
        'Hello, World!!'
      ))
    ));
  }
}
?>
