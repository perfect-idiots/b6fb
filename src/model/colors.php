<?php
require_once __DIR__ . '/../lib/utils.php';

abstract class ThemeColorSet extends FixedArrayLoader {}

class LightThemeColors extends ThemeColorSet {
  static protected function filename(): string {
    return __DIR__ . '/colors/light.php';
  }
}

class DarkThemeColors extends ThemeColorSet {
  static protected function filename(): string {
    return __DIR__ . '/colors/dark.php';
  }
}
?>
