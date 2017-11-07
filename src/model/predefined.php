<?php
require_once __DIR__ . '/../lib/yaml.php';
require_once __DIR__ . '/../lib/utils.php';

class YamlObjectLoader extends LazyLoadedDataContainer {
  protected function load(): array {
    return spyc_load_file($this->param);
  }
}

class PredefinedGames extends YamlObjectLoader {
  public function __construct() {
    parent::__construct(__DIR__ . '/predefined/games.yaml');
  }
}

class PredefinedGenres extends YamlObjectLoader {
  public function __construct() {
    parent::__construct(__DIR__ . '/predefined/genres.yaml');
  }
}
?>
