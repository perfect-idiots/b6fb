<?php
class CaseConverter {
  private $words;

  public function __construct(array $words) {
    if (!$this->validateWords($words)) {
      throw new TypeError('Passed array contains non-string elements');
    }

    $this->words = $words;
  }

  static private function validateWords(array $words): bool {
    foreach ($words as $word) {
      if (gettype($word) != 'string') return false;
    }

    return true;
  }

  static public function fromPascalCase(string $text): self {
    $words = array();
    $current = '';

    foreach (str_split($text) as $char) {
      $lowcase = strtolower($char);

      if ($lowcase == $char) {
        $current .= $lowcase;
      } else {
        $current and array_push($words, $current);
        $current = $lowcase;
      }
    }

    array_push($words, $current);
    return new static($words);
  }

  static public function fromKebabCase(string $text): self {
    $words = array();
    $current = '';

    foreach ($text as $char) {
      if (($char >= 'A' && $char <= 'Z') || ($char >= 'a' && $char <= 'z')) {
        $current .= strtolower($char);
      } else {
        array_push($words, $current);
      }
    }

    array_push($words, $current);
    return new static($words);
  }

  public function toKebabCase(string $dash = '-'): string {
    return implode($dash, $this->words);
  }
}

class DataContainer {
  protected $data;

  public function __construct(array $data = array()) {
    $this->data = $data;
  }

  static public function instance(array $data = array()): self {
    return new static($data);
  }

  public function getData(): array {
    return $this->data;
  }

  public function get($key) {
    return $this->data[$key];
  }

  public function set($key, $value): self {
    return static::assign(array($key => $value));
  }

  public function assign(array $data): self {
    return new static(array_merge($this->data, $data));
  }

  public function merge(self $addend): self {
    return static::assign($addend->getData());
  }
}
?>
