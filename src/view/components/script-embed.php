<?php
require_once __DIR__ . '/base.php';

class ScriptEmbed implements Component {
  private $attr;

  public function __construct(string $type, array $attr = []) {
    $this->attr = array_merge(['type' => $type], $attr);
  }

  public function render(): Component {
    return HtmlElement::create('script', $this->attr);
  }
}

abstract class TypedScriptEmbed extends ScriptEmbed {
  public function __construct(array $attr = []) {
    parent::__construct(static::type(), $attr);
  }

  static public function src(string $src, array $attr = []): self {
    return static::merge(['src' => $src], $attr);
  }

  static public function text(string $text, array $attr = []): self {
    return static::merge([new UnescapedText($text)], $attr);
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

  static public function dump($data, int $options = 0, array $attr = []): self {
    return self::text(json_encode($data), $attr);
  }
}
?>
