<?php
require_once __DIR__ . '/base.php';

class CssView implements Component {
  private $css;

  public function __construct(string $css) {
    $this->css = $css;
  }

  static public function fromFile(string $filename): self {
    return new self(file_get_contents($filename));
  }

  public function render(): Component {
    return HtmlElement::create('style', array(
      'type' => 'text/css',
      new UnescapedText($this->css)
    ));
  }
}
?>
