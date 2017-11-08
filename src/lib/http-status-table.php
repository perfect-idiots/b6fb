<?php
require_once __DIR__ . '/utils.php';

class HttpStatusTable extends LazyLoadedDataContainer {
  protected function load(): array {
    return require __DIR__ . '/../vendor/http-status-table/status-to-message.php';
  }
}
?>
