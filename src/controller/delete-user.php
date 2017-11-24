<?php
require_once __DIR__ . '/security.php';

class DeleteUser extends LoginDoubleChecker {
  public function checkPermission(): bool {
    $login = $this->get('login');
    return $login->username() === $this->get('username') || $login->isAdmin();
  }

  public function delete(string $username): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('delete-user')
      ->executeOnce([$username])
    ;
  }
}
?>
