<?php
require_once __DIR__ . '/security.php';

class UserCounter extends LoginDoubleChecker {
  public function countUsers(): int {
    $this->verify();

    [[$result]] = $this
      ->get('db-query-set')
      ->get('count-users')
      ->executeOnce([], 1)
      ->fetch()
    ;

    return $result;
  }
}
?>
