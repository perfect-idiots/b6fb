<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/prerendered.php';
require_once __DIR__ . '/../../lib/utils.php';

class TextAreaElement extends RawDataContainer implements Component {
  public function render(): Component {
    return new Minified(HtmlElement::create('textarea', $this->getData()));
  }

  static public function text(string $text): self {
    return new static([$text]);
  }
}
?>
