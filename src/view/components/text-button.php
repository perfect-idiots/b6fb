<?php
require_once __DIR__ . '/base.php';

class TextButton implements Component {
  private $attr;

  public function __construct($attr = []) {
    $this->attr = $attr;
  }

  public function render(): Component {
    return HtmlElement::create('button', $this->attr);
  }
}

class PrimaryButton extends TextButton {}
class SecondaryButton extends TextButton {}
?>
