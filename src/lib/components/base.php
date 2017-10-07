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

    $this->attributes = Element::getArrayKey($props, 'attributes');
    $this->classes = Element::getArrayKey($props, 'classes');
    $this->style = Element::getArrayKey($props, 'style');
    $this->dataset = Element::getArrayKey($props, 'dataset');
  }

  private function getArrayKey(array $array, string $key) {
    return array_key_exists($key, $array) && $array[$key] ? $array[$key] : array();
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
