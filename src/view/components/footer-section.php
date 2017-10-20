<?php
require_once __DIR__ . '/base.php';

class FooterSection implements Component {
  public function render(): Component {
    return HTMLElement::create('footer');
  }
}
?>
