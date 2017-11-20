<?php
require_once __DIR__ . '/../model/database.php';

class GameInserter {
  private $addingQuery, $checkingQuery;
  const GENRE_SEPARATOR = ',';

  public function __construct(DatabaseQuerySet $dbQuerySet) {
    $this->addingQuery = $dbQuerySet->get('add-game');
    $this->checkingQuery = $dbQuerySet->get('game-existence');
  }

  public function add(array $param): DatabaseQuerySingleResult {
    [
      'id' => $id,
      'name' => $name,
      'genre' => $genre,
      'description' => $description,
    ] = $param;

    $args = [
      $id,
      $name,
      self::serializeGenres($genres),
      $description,
    ];

    return $this->addingQuery->executeOnce($args);
  }

  static private function serializeGenres(array $genres): string {
    return implode(static::GENRE_SEPARATOR, $genres);
  }

  static private function unserializeGenres(string $genres): array {
    return explode(static::GENRE_SEPARATOR, $genres);
  }

  public function exists(string $id): bool {
    [[$existence]] = $this->checkingQuery->executeOnce([$id], 1);
    return $existence > 0;
  }
}
?>
