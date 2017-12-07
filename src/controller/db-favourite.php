<?php
require_once __DIR__ . '/security.php';

class FavouriteManager extends LoginDoubleChecker {
  public function clear(): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('clear-favourites')
      ->executeOnce([])
    ;
  }

  public function reset(): void {
    $this->verify();
    $this->clear();
  }
}
?>
