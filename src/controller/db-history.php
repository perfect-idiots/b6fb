<?php
require_once __DIR__ . '/security.php';

class HistoryManager extends LoginDoubleChecker {
  public function clear(): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('clear-history')
      ->executeOnce([])
    ;
  }

  public function reset(): void {
    $this->verify();
    $this->clear();
  }
}
?>
