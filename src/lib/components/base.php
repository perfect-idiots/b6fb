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
  public $tag, $attributes, $children, $classes, $style, $dataset;

  public function __construct(string $tag, array $props = array(), array $children = array()) {
    $this->tag = $tag;
    $this->children = $children;

    function get(array $array, string $key, array $default): array {
      return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    $this->attributes = get($props, 'attributes', array());
    $this->classes = get($props, 'classes', array());
    $this->style = get($props, 'style', array());
    $this->dataset = get($props, 'dataset', array());
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
