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

    if ($swf->mimetype() !== 'application/x-shockwave-flash') {
      throw new GameInvalidMimeException("Game's mime type is not 'application/x-shockwave-flash'");
    }

    if ($img->mimetype() !== 'image/jpeg') {
      throw new GameInvalidMimeException("Image's mime type is not 'image/jpeg");
    }

    $args = [
      $id,
      $name,
      self::serializeGenres($genres),
      $description,
    ];

    $addingQuery = $this->get('db-query-set')->get('adding-query');
    $dbResult = $addingQuery->executeOnce($args);
    $swfResult = $swf->move(self::swfPath($id));
    $imgResult = $img->move(self::imgPath($id));

    return [
      'db' => $dbResult,
      'swf' => $swfResult,
      'img' => $imgResult,
    ];
  }

  public function delete(string $id): ?array {
    $this->verify();
    if (!$this->exists($id)) return null;

    $dbResult = $this
      ->get('db-query-set')
      ->get('delete-game')
      ->executeOnce()
    ;

    $swfResult = unlink(self::swfPath($id));
    $imgResult = unlink(self::imgPath($id));

    return [
      'db' => $dbResult,
      'swf' => $swfResult,
      'img' => $imgResult,
    ];
  }

  public function exists(string $id): bool {
    [[$existence]] = $this->checkingQuery->executeOnce([$id], 1);
    return $existence > 0;
  }

  static private function swfPath(string $name): string {
    return __DIR__ . '/../storage/game-swfs/' . $name;
  }

  static private function imgPath(string $name): string {
    return __DIR__ . '/../storage/game-imgs/' . $name;
  }

  static private function serializeGenres(array $genres): string {
    return implode(static::GENRE_SEPARATOR, $genres);
  }

  static private function unserializeGenres(string $genres): array {
    return explode(static::GENRE_SEPARATOR, $genres);
  }
}

class GameInsertingException extends Exception {}
class GameInvalidIdException extends GameInsertingException {}
class GameDuplicatedException extends GameInsertingException {}
class GameInvalidMimeException extends GameInsertingException {}
?>
