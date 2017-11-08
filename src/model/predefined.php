<?php
require_once __DIR__ . '/../lib/yaml.php';

abstract class PredefinedData extends FixedYamlObjectLoader {}

class PredefinedGames extends PredefinedData {
  static protected function filename(): string {
    return __DIR__ . '/predefined/games.yaml';
  }
}

class PredefinedGenres extends PredefinedData {
  static protected function filename(): string {
    return __DIR__ . '/predefined/genres.yaml';
  }
}
?>
