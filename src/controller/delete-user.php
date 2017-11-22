<?php>
require_once __DIR__ . '/security.php';

class DeleteUser extends LoginDoubleChecker {
  public function delete(string $query): int {
    $this->verify();
  }
}
?>
