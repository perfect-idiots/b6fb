<?php
require_once __DIR__ . '/../lib/utils.php';

class BlockSize extends RawDataContainer {
  static public function xy($width, $height): self {
    return new self([$width, $height]);
  }

  public function width(): int {
    return static::get(0);
  }

  public function height(): int {
    return static::get(1);
  }
}

class VectorSize extends RawDataContainer {}

class SizeSet extends LazyLoadedDataContainer {
  protected function load(): array {
    $begin = Tree::instance([
      'logo' => BlockSize::xy(120, 60),
      'logo-line-height' => 50,
    ])->flat('-');

    $middle = [];
    foreach ($begin as $prefix => $size) {
      if ($size instanceof BlockSize) {
        $middle["$prefix-width"] = $size->width();
        $middle["$prefix-height"] = $size->height();
        $middle["$prefix-size-block"] = $size;
      } else if ($size instanceof VectorSize) {
        foreach ($size->getData() as $index => $element) {
          $middle["$prefix-$index"] = $element;
        }
        $middle["$prefix-size-vector"] = $size;
      } else {
        $middle[$prefix] = $size;
      }
    }

    $final = [];
    foreach ($middle as $key => $value) {
      $final[$key] = self::transform($value);
    }

    return $final;
  }

  static private function transform($value): string {
    switch (gettype($value)) {
      case 'string':
        return $value;
      case 'integer':
      case 'double':
        return "{$value}px";
      case 'object':
        if ($value instanceof BlockSize) {
          $width = self::transform($value->width());
          $height = self::transform($value->height());
          return "width: $width; height: $height;";
        }
        if ($value instanceof VectorSize) {
          return implode(' ', $value->getData());
        }
        throw new TypeError('Cannot transform this type of object: ' . get_class($value));
      default:
        throw new TypeError('Cannot transform this type of value: ' . gettype($value));
    }
  }
}
?>
