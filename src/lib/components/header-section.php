<?php
require_once __DIR__ . '/base.php';

class HeaderSection implements Component {
  public function render(): Component {
    return HTMLElement::create('header');
  }
}
?>
