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
    return new self($words);
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
    return new self($words);
  }

  public function toKebabCase(string $dash = '-'): string {
    return implode($dash, $this->words);
  }
}
?>
