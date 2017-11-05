<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/stylus.php';

class StylusView implements Component {
  private $css;

  public function __construct(Stylus $stylus) {
    $this->css = $stylus->toString();
  }

  public static function fromString(Stylus $stylus, string $styl): self {
    return new self($stylus->fromString($styl));
  }

  public static function fromFile(Stylus $stylus, string $filename): self {
    return new self($stylus->fromFile($filename));
  }

  public function render(): Component {
    return HtmlElement::create('style', array(
      'type' => 'text/css',
      new UnescapedText($this->css)
    ));
  }
}
?>
