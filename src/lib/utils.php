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

interface DataContainer {
  public function getData(): array;
  public function get($key);
  public function set($key, $value): DataContainer;
  public function except($key): DataContainer;
  public function assign(array $data): DataContainer;
  public function without(array $data): DataContainer;
  public function merge(DataContainer $addend): DataContainer;
}

class RawDataContainer implements DataContainer {
  private $data;

  public function __construct(array $data = array()) {
    $this->data = $data;
  }

  static public function instance(array $data = array()): DataContainer {
    return new static($data);
  }

  public function getData(): array {
    return $this->data;
  }

  public function get($key) {
    return $this->data[$key];
  }

  public function set($key, $value): DataContainer {
    return static::assign(array($key => $value));
  }

  public function except($key): DataContainer {
    return static::without(array($key));
  }

  public function assign(array $data): DataContainer {
    return new static(array_merge($this->data, $data));
  }

  public function without(array $keys): DataContainer {
    return new static(
      array_diff_key($this->getData(), array_flip((array) $keys))
    );
  }

  public function merge(DataContainer $addend): DataContainer {
    return static::assign($addend->getData());
  }

  public function getDefault($key, $default) {
    $data = $this->getData();
    return array_key_exists($key, $data) ? $data[$key] : $default;
  }
}

abstract class LazyLoadedDataContainer implements DataContainer {
  protected $param;
  private $state, $data;

  private function __construct(bool $state, $param, array $data = array()) {
    $this->state = $state;
    $this->param = $param;
    $this->data = $data;
  }

  static public function instance($param = null): self {
    return new static(false, $param, array());
  }

  abstract protected function load(): array;

  public function getData(): array {
    $this->firstRun();
    return $this->data;
  }

  public function get($key) {
    $this->firstRun();
    return $this->data[$key];
  }

  public function set($key, $value): DataContainer {
    $this->firstRun();
    return static::assign(array($key => $value));
  }

  public function except($key): DataContainer {
    return static::without(array($key));
  }

  public function assign(array $data): DataContainer {
    $this->firstRun();
    return new static(true, null, array_merge($this->data, $data));
  }

  public function without(array $keys): DataContainer {
    $this->firstRun();
    return new static(
      true, null,
      array_diff_key($this->getData(), array_flip((array) $keys))
    );
  }

  public function merge(DataContainer $addend): DataContainer {
    $this->firstRun();
    return static::assign($addend->getData());
  }

  private function firstRun(): void {
    if ($this->state) return;
    $this->state = true;
    $this->data = $this->load();
  }
}

class HttpException extends Exception {}
class NotFoundException extends HttpException {}
?>
