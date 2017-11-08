<?php
require_once __DIR__ . '/../lib/yaml.php';

abstract class ThemeColorSet extends FixedYamlObjectLoader {}

class LightThemeColors extends ThemeColorSet {
  static protected function filename(): string {
    return __DIR__ . '/colors/light.yaml';
  }
}

class DarkThemeColors extends ThemeColorSet {
  static protected function filename(): string {
    return __DIR__ . '/colors/dark.yaml';
  }
}
?>
