<?php
require_once __DIR__ . '/security.php';

class AdminManager extends LoginDoubleChecker {
  public function clear(): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('clear-admins')
      ->executeOnce([])
    ;
  }

  public function reset(): void {
    $this->verify();
    $this->clear();
  }

  public function updatePassword(string $username, string $password): void {
    $this->verify();
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $this
      ->get('db-query-set')
      ->get('update-admin-password')
      ->executeOnce([$hash, $username])
    ;
  }
}
?>
