<?php
interface Component {
  public function render(): Component;
}

class PrimaryComponent implements Component {
  public function render(): Component {
    throw new TypeError('Cannot render a primary component.');
  }
}

abstract class Element extends PrimaryComponent {
  private const SPECIAL_FIELDS = array(
    'attributes', 'classes', 'style', 'dataset', 'children'
  );

  public $tag, $attributes, $children, $classes, $style, $dataset;

  public function __construct(string $tag, array $props = array(), array $children = array()) {
    $this->tag = $tag;
    $this->children = $children;

    $this->attributes = Element::getArrayKey($props, 'attributes');
    $this->classes = Element::getArrayKey($props, 'classes');
    $this->style = Element::getArrayKey($props, 'style');
    $this->dataset = Element::getArrayKey($props, 'dataset');
  }

  abstract public function tagClosingStyle(): string;

  static public function create(string $tag, $desc = array()): self {
    if (gettype($desc) != 'array') return static::create($tag, array($desc));

    $attributes = Element::getArrayKey($desc, 'attributes');
    $classes = Element::getArrayKey($desc, 'classes');
    $style = Element::getArrayKey($desc, 'style');
    $dataset = Element::getArrayKey($desc, 'dataset');
    $children = Element::getArrayKey($desc, 'children');

    foreach($desc as $key => $value) {
      if (is_long($key)) {
        array_push($children, $value);
      } else if (!in_array($key, Element::SPECIAL_FIELDS)) {
        $attributes[$key] = $value;
      }
    }

    return new static(
      $tag,
      array(
        'attributes' => $attributes,
        'classes' => $classes,
        'style' => $style,
        'dataset' => $dataset
      ),
      array_map(
        function ($x) {
          return $x instanceof Component ? $x : new TextNode((string) $x);
        },
        $children
      )
    );
  }

  static public function nested(array $tags, $desc = array()) {
    return sizeof($tags)
      ? static::create(
        $tags[0],
        static::nested(array_slice($tags, 1), $desc)
      )
      : $desc
    ;
  }

  static private function getArrayKey(array $array, string $key): array {
    return array_key_exists($key, $array) && $array[$key] ? $array[$key] : array();
  }
}

class XmlElement extends Element {
  public function tagClosingStyle(): string {
    return sizeof($this->element) ? 'non-empty' : 'self-close';
  }
}

class HtmlElement extends Element {
  private const EMPTY_TAGS = array(
    'area', 'base', 'br', 'col', 'embed',
    'hr', 'img', 'input', 'keygen', 'link',
    'meta', 'param', 'source', 'track', 'wbr'
  );

  public function tagClosingStyle(): string {
    if(in_array($this->tag, HtmlElement::EMPTY_TAGS)) return 'self-close';
    if(sizeof($this->children)) return 'non-empty';
    return 'pair-close';
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
