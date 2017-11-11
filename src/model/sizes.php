<?php
require_once __DIR__ . '/../lib/utils.php';

class SizeSet extends LazyLoadedDataContainer {
  protected function load(): array {
    $begin = [
      'logo' => [120, 60],
    ];

    $middle = [];
    foreach ($begin as $prefix => $size) {
      if (gettype($size) === 'array') {
        $middle["$prefix-width"] = $size[0];
        $middle["$prefix-height"] = $size[1];
        $middle["$prefix-size-block"] = $size;
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
      case 'array':
        $width = self::transform($value[0]);
        $height = self::transform($value[1]);
        return "width: $width; height: $height;";
      default:
        throw new TypeError('Cannot transform this type of value: ' . gettype($value));
    }
  }
}
?>
