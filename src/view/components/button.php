<?php
require_once __DIR__ . '/base.php';

class Button implements Component {
  private $attr;

  public function __construct($attr = []) {
    $this->attr = $attr;
  }

  public function render(): Component {
    return HtmlElement::create('button', $this->attr);
  }
}

class PrimaryButton extends Button {}
class SecondaryButton extends Button {}
?>
