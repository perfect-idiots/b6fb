<?php
require_once __DIR__ . '/security.php';

class DatabaseRowCounter extends LoginDoubleChecker {
  private function count(string $query): int {
    $this->verify();

    [[$result]] = $this
      ->get('db-query-set')
      ->get($query)
      ->executeOnce([], 1)
      ->fetch()
    ;

    return $result;
  }

  public function countUsers(): int {
    return $this->count('count-users');
  }

  public function countGames(): int {
    return $this->count('count-games');
  }
}
?>
