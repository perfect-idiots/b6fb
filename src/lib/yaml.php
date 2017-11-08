<?php
require_once __DIR__ . '/../vendor/yaml/Spyc.php';
require_once __DIR__ . '/utils.php';

class YamlObjectLoader extends LazyLoadedDataContainer {
  protected function load(): array {
    return spyc_load_file($this->param);
  }
}

abstract class FixedYamlObjectLoader extends YamlObjectLoader {
  abstract static protected function filename(): string;

  static public function create(): self {
    return static::instance(static::filename());
  }
}
?>
