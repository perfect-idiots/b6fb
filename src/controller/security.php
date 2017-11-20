<?php
require_once __DIR__ . '/login.php';
require_once __DIR__ . '/../lib/utils.php';

abstract class LoginDoubleChecker extends RawDataContainer {
  static protected function requiredFieldSchema(): array {
    return [
      'login' => 'LoginInfo',
      'db-query-set' => 'DatabaseQuerySet',
    ];
  }

  public function checkAll(): bool {
    return $this->checkLogin() && $this->checkPermission();
  }

  public function checkLogin(): bool {
    return Login::checkLogin(
      $this
        ->get('login')
        ->merge($this)
        ->getData()
    )->isLoggedIn();
  }

  public function checkPermission(): bool {
    return $this->get('login')->isAdmin();
  }
}

class SecurityException extends Exception {}
?>
