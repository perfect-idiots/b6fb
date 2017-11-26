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

  public function verify(): void {
    if (!$this->checkAll()) throw SecurityException::permission();
  }

  public function checkAll(): bool {
    return $this->checkLogin() && $this->checkPermission();
  }

  public function checkLogin(): bool {
    [
      'cookie' => $cookie,
      'session' => $session,
      'login' => $login,
      'db-query-set' => $dbQuerySet,
    ] = $this->getData();

    return Login::checkLogin(
      $login->assign([
        'cookie' => $cookie,
        'session' => $session,
        'db-query-set' => $dbQuerySet,
      ])->getData()
    )->isLoggedIn();
  }

  public function checkPermission(): bool {
    return $this->get('login')->isAdmin();
  }
}

class SecurityException extends Exception {
  static public function permission(): self {
    return new static('Permission Denied');
  }
}
?>
