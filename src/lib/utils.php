<?php
function splitAndCombine(string $keys, string $values, string $seprgx = '/\s*,\s*/') {
  return array_combine(
    preg_split($seprgx, $keys),
    preg_split($seprgx, $values)
  );
}

class ClassChecker {
  private $parents, $implements;

  public function __construct(string $subject) {
    $this->parents = class_parents($subject);
    $this->implements = class_implements($subject);
  }

  static public function instance(string $subject): self {
    return new static($subject);
  }

  public function getParents(): array {
    return $this->parents;
  }

  public function getImplements(): array {
    return $this->implements;
  }

  public function didExtended(string $class): bool {
    return in_array($class, $this->getParents());
  }

  public function didImplemented(string $interface): bool {
    return in_array($interface, $this->getImplements());
  }
}

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
    $words = [];
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
    $words = [];
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
  public function hasKey($key): bool;
  public function hasValue($key): bool;
  public function get($key);
  public function set($key, $value): DataContainer;
  public function except($key): DataContainer;
  public function clone(): DataContainer;
  public function assign(array $data): DataContainer;
  public function without(array $data): DataContainer;
  public function merge(DataContainer $addend): DataContainer;
  public function getDefault($key, $default);
}

class RawDataContainer implements DataContainer {
  private $data;

  public function __construct(array $data = []) {
    static::verifyFields($data);
    $this->data = $data;
  }

  static protected function verifyFields(array $data): void {
    $schema = static::requiredFieldSchema();

    foreach ($schema as $key => $type) {
      if (!array_key_exists($key, $data)) {
        throw new TypeError("Field '$key' is not provided");
      }

      if ($type && gettype($data[$key]) !== $type && !($data[$key] instanceof $type)) {
        throw new TypeError("Field '$key' is not a(n) $type");
      }
    }
  }

  static protected function requiredFieldSchema(): array {
    return [];
  }

  static public function instance(array $data = []): DataContainer {
    return new static($data);
  }

  public function getData(): array {
    return $this->data;
  }

  public function hasKey($key): bool {
    return array_key_exists($key, $this->getData());
  }

  public function hasValue($value): bool {
    return in_array($value, $this->getData());
  }

  public function get($key) {
    return $this->data[$key];
  }

  public function set($key, $value): DataContainer {
    return static::assign([$key => $value]);
  }

  public function except($key): DataContainer {
    return static::without([$key]);
  }

  public function clone(): DataContainer {
    return new static($this->getData());
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

  private function __construct(bool $state, $param, array $data = []) {
    $this->state = $state;
    $this->param = $param;
    $this->data = $data;
  }

  static public function instance($param = null): self {
    $error = static::validateParam($param);
    if ($error) throw new Error($error);
    return new static(false, $param, []);
  }

  abstract protected function load(): array;

  static public function validateParam($param): string {
    return '';
  }

  static protected function transformParam($param) {
    return $param;
  }

  public function getData(): array {
    $this->firstRun();
    return $this->data;
  }

  public function hasKey($key): bool {
    $this->firstRun();
    return array_key_exists($key, $this->getData());
  }

  public function hasValue($value): bool {
    $this->firstRun();
    return in_array($value, $this->getData());
  }

  public function get($key) {
    $this->firstRun();
    return $this->data[$key];
  }

  public function set($key, $value): DataContainer {
    $this->firstRun();
    return static::assign([$key => $value]);
  }

  public function except($key): DataContainer {
    return static::without([$key]);
  }

  public function clone(): DataContainer {
    $param = static::transformParam($this->param);

    return $this->state
      ? new static(true, $param, $this->getData())
      : new static(false, $param, [])
    ;
  }

  public function assign(array $data): DataContainer {
    $this->firstRun();
    return new static(
      true,
      static::transformParam($this->param),
      array_merge($this->data, $data)
    );
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

  public function getDefault($key, $default) {
    $this->firstRun();
    $data = $this->getData();
    return array_key_exists($key, $data) ? $data[$key] : $default;
  }

  private function firstRun(): void {
    if ($this->state) return;
    $this->state = true;
    $this->data = $this->load();
  }
}

class ArrayLoader extends LazyLoadedDataContainer {
  public function load(): array {
    return require $this->param;
  }
}

abstract class FixedArrayLoader extends ArrayLoader {
  static abstract protected function filename(): string;

  static public function create(): self {
    return static::instance(static::filename());
  }
}

class Tree {
  private $tree;

  public function __construct(array $tree) {
    $this->tree = $tree;
  }

  static public function instance(array $tree): self {
    return new static($tree);
  }

  public function flat(string $separator = '/', $omitsepon = null): iterable {
    foreach ($this->tree as $prefix => $outer) {
      if (is_iterable($outer)) {
        $flatten = static::instance($outer, $omitsepon)->flat($separator, $omitsepon);
        foreach ($flatten as $suffix => $inner) {
          yield $prefix . ($suffix === $omitsepon ? '' : ($separator . $suffix)) => $inner;
        }
      } else {
        yield $prefix => $outer;
      }
    }
  }
}

class HttpException extends Exception {}
class NotFoundException extends HttpException {}
?>
