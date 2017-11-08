<?php
require_once __DIR__ . '/utils.php';

class HttpStatusTable extends FixedArrayLoader {
  static protected function filename(): string {
    return __DIR__ . '/../vendor/http-status-table/status-to-message.php';
  }
}
?>
