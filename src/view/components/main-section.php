<?php
require_once __DIR__ . '/base.php';

class MainSection implements Component {
  public function render(): Component {
    return HtmlElement::create('main');
  }
}
?>
