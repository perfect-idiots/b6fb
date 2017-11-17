<?php
require_once __DIR__ . '/../lib/utils.php';
class HttpData extends RawDataContainer {
  public function update(array &$target): void {
    $target = $this->getData();
  }
}
?>
