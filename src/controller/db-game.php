<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/uploaded-files.php';
require_once __DIR__ . '/security.php';

class GameManager extends LoginDoubleChecker {
  const GENRE_SEPARATOR = ',';

  public function add(array $param): array {
    $this->verify();

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

    $addingQuery = $this->get('db-query-set')->get('adding-query');
    $dbResult = $addingQuery->executeOnce($args);
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
