<?php
require_once __DIR__ . '/base.php';

class ScriptEmbed implements Component {
  private $attr;

  public function __construct(string $type, array $attr = array()) {
    $this->attr = array_merge(array('type' => $type), $attr);
  }

  public function render(): Component {
    return HtmlElement::create('script', $this->attr);
  }
}

abstract class TypedScriptEmbed extends ScriptEmbed {
  public function __construct(array $attr = array()) {
    parent::__construct(static::type(), $attr);
  }

  static public function src(string $src, array $attr = array()): self {
    return static::merge(array('src' => $src), $attr);
  }

  static public function text(string $text, array $attr = array()): self {
    return static::merge(array(new UnescapedText($text)), $attr);
  }

  static private function merge(array $left, array $right): self {
    return new static(array_merge($left, $right));
  }

  abstract static function type(): string;
}

class JavascriptEmbed extends TypedScriptEmbed {
  static function type(): string {
    return 'text/javascript';
  }
}

abstract class DataEmbed extends TypedScriptEmbed {}

class JsonDataEmbed extends DataEmbed {
  static function type(): string {
    return 'application/json';
  }
}
?>
