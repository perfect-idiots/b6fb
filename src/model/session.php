<?php
require_once __DIR__ . '/../lib/utils.php';

class Session extends LazyLoadedDataContainer {
  protected function load(): array {
    session_start();
    return $_SESSION;
  }

  public function update(): void {
    session_unset();

    foreach (array_keys($_SESSION) as $key) {
      unset($_SESSION[$key]);
    }

    foreach ($this->getData() as $key => $value) {
      $_SESSION[$key] = $value;
    }
  }

  private function addSessionValues(array $tobeset): void {
    foreach ($tobeset as $key => $value) {
      $_SESSION[$key] = $value;
    }
  }

  public function destroy(): bool {
    return session_destroy();
  }
}
?>
