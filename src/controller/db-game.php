<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/uploaded-files.php';

class GameInserter {
  private $addingQuery, $checkingQuery;
  const GENRE_SEPARATOR = ',';

  public function __construct(DatabaseQuerySet $dbQuerySet) {
    $this->addingQuery = $dbQuerySet->get('add-game');
    $this->checkingQuery = $dbQuerySet->get('game-existence');
  }

  public function add(array $param): array {
    [
      'id' => $id,
      'name' => $name,
      'genre' => $genre,
      'description' => $description,
      'swf' => $swf,
      'img' => $img,
    ] = $param;

    if (!preg_match('/^[a-z]+(([a-z]+-)*[a-z]+)?$/i', $id)) {
      throw new GameInvalidIdException("Game id '$id' is invalid");
    }

    if ($this->exists($id)) {
      throw new GameDuplicatedException("Game '$id' already exist");
    }

    $args = [
      $id,
      $name,
      self::serializeGenres($genres),
      $description,
    ];

    $dbResult = $this->addingQuery->executeOnce($args);
    $storage = __DIR__ . '/../storage';
    $swfDir = "$storage/game-swfs";
    $imgDir = "$storage/game-imgs";
    $swfResult = $swf->move("$swfDir/$id");
    $imgResult = $img->move("$imgDir/$id");

    return [
      'db' => $dbResult,
      'swf' => $swfResult,
      'img' => $imgResult,
    ];
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

class GameInsertingException extends Exception {}
class GameInvalidIdException extends GameInsertingException {}
class GameDuplicatedException extends GameInsertingException {}
?>
