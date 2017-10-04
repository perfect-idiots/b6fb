<?php
interface Component {
  public function render(): Component;
}

class PrimaryComponent implements Component {
  public function render(): Component {
    throw new Exception('Cannot render a primary component.');
  }
}

class Element extends PrimaryComponent {
  public $tag, $attributes, $children, $classes, $style, $data;

  public function __construct(string $tag, array $props, array $children) {
    $this->tag = $tag;
    $this->children = $children || array();

    if (!$props) $props = array();
    $this->attributes = $props['attributes'] || array();
    $this->classes = $props['classes'] || array();
    $this->style = $props['style'] || array();
    $this->data = $props['data'] || array();
  }
}

abstract class TextBase extends PrimaryComponent {
  abstract public function getText(): string;
}

class TextNode extends TextBase {
  private $text;

  public function __construct(string $text) {
    $this->text = $text;
  }

  public function getText(): string {
    return htmlspecialchars($this->text);
  }
}

class UnescapedText extends TextBase {
  private $text;

  public function __construct(string $text) {
    $this->text = $text;
  }

  public function getText(): string {
    return $this->text;
  }
}
?>
