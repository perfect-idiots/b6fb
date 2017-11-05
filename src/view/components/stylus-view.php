<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/stylus.php';
use NodejsPhpFallback\Stylus;

class StylusView implements Component {
  private $css;

  public function __construct(Stylus $stylus) {
    $this->css = $stylus->getCss();
  }

  static public function instance(string $str): self {
    return new self(new Stylus($str));
  }

  public function render(): Component {
    return HtmlElement::create('style', array(
      'type' => 'text/css',
      new UnescapedText($this->css)
    ));
  }
}
?>
