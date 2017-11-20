<?php
require_once __DIR__ . '/login.php';
require_once __DIR__ . '/../lib/utils.php';

class LoginDoubleChecker extends RawDataContainer {
  static protected function requiredFieldSchema(): array {
    return [
      'login' => 'LoginInfo',
      'db-query-set' => 'DatabaseQuerySet',
    ];
  }

  public function checkLogin(): bool {
    return Login::checkLogin(
      $this
        ->get('login')
        ->merge($this)
        ->getData()
    )->isLoggedIn();
  }
}
?>
