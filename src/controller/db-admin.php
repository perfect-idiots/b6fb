<?php
require_once __DIR__ . '/security.php';

class AdminManager extends LoginDoubleChecker {
  public function reset(): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('clear-admins')
      ->executeOnce([])
    ;
  }
}
?>
