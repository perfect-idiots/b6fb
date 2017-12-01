<?php
require_once __DIR__ . '/db-game-genre.php';
require_once __DIR__ . '/../model/predefined.php';

class GenreManager extends GameGenreRelationshipManager {
  public function info(string $id): ?array {
    $dbResult = $this
      ->get('db-query-set')
      ->get('genre-info')
      ->executeOnce([$id], 2)
      ->fetch()
    ;

    if (!sizeof($dbResult)) return null;

    [[$name]] = $dbResult;
    return [
      $name,
      $id,
      'name' => $name,
      'id' => $id,
    ];
  }

  public function delete(string $id): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('delete-genre')
      ->executeOnce([$id])
    ;
  }

  public function clear(): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('clear-genres')
      ->executeOnce([])
    ;

    parent::clear();
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
    $list = $this
      ->get('db-query-set')
      ->get('list-genres')
      ->executeOnce([], 2)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'name' => $row[1],
        ]);
      },
      $list
    );
  }
}
?>
