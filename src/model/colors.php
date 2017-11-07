<?php
require_once __DIR__ . '/../lib/yaml.php';

class ThemeColorSet extends YamlObjectLoader {}

class LightThemeColors extends ThemeColorSet {
  public function __construct() {
    parent::__construct(__DIR__ . '/colors/light.yaml');
  }
}

class DarkThemeColors extends ThemeColorSet {
  public function __construct() {
    parent::__construct(__DIR__ . '/colors/dark.yaml');
  }
}
?>
