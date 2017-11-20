<?php
require_once __DIR__ . '/../model/database.php';

class GameInserter {
  private $statement;
  const GENRE_SEPARATOR = ',';

  public function __construct(DatabaseQuerySet $dbQuerySet) {
    $this->statement = $dbQuerySet->get('add-game');
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

    return $this->statement->executeOnce($args);
  }

  static private function serializeGenres(array $genres): string {
    return implode(static::GENRE_SEPARATOR, $genres);
  }

  static private function unserializeGenres(string $genres): array {
    return explode(static::GENRE_SEPARATOR, $genres);
  }
}
?>
