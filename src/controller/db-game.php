<?php
require_once __DIR__ . '/../model/database.php';
require_once __DIR__ . '/../model/uploaded-files.php';
require_once __DIR__ . '/../model/predefined.php';
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/db-game-genre.php';

class GameManager extends GameGenreRelationshipManager {
  public function info(string $id): ?array {
    $dbResult = $this
      ->get('db-query-set')
      ->get('game-info')
      ->executeOnce([$id], 3 + 2)
      ->fetch()
    ;

    if (!sizeof($dbResult)) return null;

    [$row] = $dbResult;

    return array_merge($row, [
      'name' => $row[0],
      'genre' => splitAndCombine($row[2], $row[1]),
      'description' => $row[3],
      'id' => $id,
    ]);
  }

  public function add(array $param): void {
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

    if (gettype($genre) !== 'array') {
      throw new TypeError("Field 'genre' must be an array of string");
    }

    $this
      ->get('db-query-set')
      ->get('add-game')
      ->executeOnce([$id, $name, $description])
    ;

    parent::addGenres($id, $genre);
    $swf->move(self::swfPath($id));
    $img->move(self::imgPath($id));
  }

  public function update(string $prevId, array $param): void {
    $this->verify();
    $id = $param['id'];

    $this
      ->get('db-query-set')
      ->get('update-game')
      ->executeOnce([
        $param['id'],
        $param['name'],
        $param['description'],
        $prevId,
      ])
    ;

    if ($id !== $prevId) {
      parent::clearGenres($prevId, $param['genre']);
      parent::addGenres($id, $param['genre']);
    }

    $mv = $id === $prevId
      ? function () {}
      : 'rename'
    ;

    foreach (['swf', 'img'] as $key) {
      $file = $param[$key];
      $pathmtd = $key . 'Path';

      if ($file) {
        unlink(self::$pathmtd($prevId));
        $file->move(self::$pathmtd($id));
      } else {
        $mv(self::$pathmtd($prevId), self::$pathmtd($id));
      }
    }
  }

  public function delete(string $id): void {
    $this->verify();
    if (!$this->exists($id)) return;

    $this
      ->get('db-query-set')
      ->get('delete-game')
      ->executeOnce([])
    ;

    parent::clearGenres($id);
    unlink(self::swfPath($id));
    unlink(self::imgPath($id));
  }

  public function reset(): void {
    $this->verify();
    $this->clear();

    $addingGameQuery = $this->get('db-query-set')->get('add-game');
    $games = PredefinedGames::create()->getData();

    foreach ($games as $id => $info) {
      $addingGameQuery->executeOnce([
        $id,
        $info['name'],
        $info['description'],
      ]);

      parent::addGenres($id, $info['genre']);

      copy(
        __DIR__ . "/../media/games/$id",
        self::swfPath($id)
      );

      copy(
        __DIR__ . "/../media/images/$id/0",
        self::imgPath($id)
      );
    }
  }

  public function clear(): void {
    $this->verify();

    foreach ($this->list() as [$name]) {
      unlink(self::swfPath($name));
      unlink(self::imgPath($name));
    }

    $this
      ->get('db-query-set')
      ->get('clear-games')
      ->executeOnce([])
    ;

    parent::clear();
  }

  public function exists(string $id): bool {
    [[$existence]] = $this
      ->get('db-query-set')
      ->get('game-existence')
      ->executeOnce([$id], 1)
      ->fetch()
    ;

    return $existence > 0;
  }

  public function list(): array {
    $list = $this
      ->get('db-query-set')
      ->get('list-games')
      ->executeOnce([], 3 + 2)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'name' => $row[1],
          'genre' => splitAndCombine($row[2], $row[3]),
          'description' => $row[4],
        ]);
      },
      $list
    );
  }

  public function getItemInfo(string $id): ?array {
    $dbResult = $this
      ->get('db-query-set')
      ->get('game-info')
      ->executeOnce([$id], 3 + 2)
      ->fetch()
    ;

    if (!sizeof($dbResult)) return null;

    [$row] = $dbResult;

    return array_merge($row, [
      'name' => $row[0],
      'genre' => splitAndCombine($row[1], $row[2]),
      'description' => $row[3],
      'id' => $id,
    ]);
  }

  public function count(): int {
    [[$count]] = $this
      ->get('db-query-set')
      ->get('count-games')
      ->executeOnce([], 1)
      ->fetch()
    ;

    return $count;
  }

  static private function swfPath(string $name): string {
    return __DIR__ . '/../storage/game-swfs/' . $name;
  }

  static private function imgPath(string $name): string {
    return __DIR__ . '/../storage/game-imgs/' . $name;
  }
}

class GameInsertingException extends Exception {}
class GameInvalidIdException extends GameInsertingException {}
class GameDuplicatedException extends GameInsertingException {}
class GameInvalidMimeException extends GameInsertingException {}
?>
