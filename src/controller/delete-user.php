<?php>
require_once __DIR__ . '/security.php';

class DeleteUser extends LoginDoubleChecker {
  static protected function requiredFieldSchema(): array {
    return array_merge(parent::requiredFieldSchema(), [
      'username' => 'string',
    ]);
  }

  public function checkPermission(): bool {
    $login = $this->get('login');
    return $login->username() === $this->get('username') || $login->isAdmin();
  }

  public function delete(string $query): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('delete-users')
      ->executeOnce()
    ;
  }
}
?>
