<?php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../model/predefined.php';

class GenreManager extends LoginDoubleChecker {
  public function clear(): void {
    $this->verify();
    $this
      ->get('db-query-set')
      ->get('clear-genres')
      ->executeOnce([])
    ;
  }

  public function reset(): void {
    $this->verify();
    $this->clear();

    $addingGenreQuery = $this->get('db-query-set')->get('add-genre');
    $genres = PredefinedGenres::create()->getData();

    foreach ($genres as $id => $name) {
      $addingGenreQuery->executeOnce([$id, $name]);
    }
  }

  public function list(): array {
    return $this
      ->get('db-query-set')
      ->get('list-genres')
      ->executeOnce([], 2)
      ->fetch()
    ;
  }
}
?>
