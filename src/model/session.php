<?php
require_once __DIR__ . '/../lib/utils.php';

class Session extends LazyLoadedDataContainer {
  protected function load(): array {
    session_start();
    return $_SESSION;
  }
}
?>
