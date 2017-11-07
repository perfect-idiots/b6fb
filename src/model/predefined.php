<?php
require_once __DIR__ . '/../lib/yaml.php';

class PredefinedData extends YamlObjectLoader {}

class PredefinedGames extends PredefinedData {
  public function __construct() {
    parent::__construct(__DIR__ . '/predefined/games.yaml');
  }
}

class PredefinedGenres extends PredefinedData {
  public function __construct() {
    parent::__construct(__DIR__ . '/predefined/genres.yaml');
  }
}
?>
