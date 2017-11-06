<?php
require_once __DIR__ . '/base.php';

class Anchor implements Component {
  private $href, $attr;

  public function __construct(string $href, array $attr) {
    $this->href = $href;
    $this->attr = $attr;
  }

  static public function withoutAttributes(string $href, $content): self {
    return new self($href, array($content));
  }

  static public function linkify(string $href, array $attr = array()): self {
    return new self($href, array_merge(array($href), $attr));
  }

  public function render(): Component {
    return HtmlElement::create('a', array_merge(
      array('href' => $this->href),
      $this->attr
    ));
  }
}
?>
