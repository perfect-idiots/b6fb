<?php
require_once __DIR__ . '/security.php';

class GameGenreRelationshipManager extends LoginDoubleChecker {
  public function addPair(string $game, string $genre): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('add-game-genre-pair')
      ->executeOnce([$game, $genre])
    ;
  }

  public function deletePair(string $game, string $genre): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('delete-game-genre-pair')
      ->executeOnce([$game, $genre])
    ;
  }

  public function getPairRelationship(array $pair): bool {
    [[$result]] = $this
      ->get('db-query-set')
      ->get('game-genre-pair-existence')
      ->executeOnce($pair, 1)
      ->fetch()
    ;

    return (bool) $result;
  }

  public function setPairRelationship(array $pair, bool $existence): void {
    $this->verify();
    [$game, $genre] = $pair;

    if ($existence) {
      $this->addPair($game, $genre);
    } else {
      $this->deletePair($game, $genre);
    }
  }

  public function getGenres(string $game): array {
    return $this
      ->get('db-query-set')
      ->get('get-all-genres-by-game')
      ->executeOnce([$game], 2)
      ->fetch()
    ;
  }

  public function getGames(string $genre): array {
    return $this
      ->get('db-query-set')
      ->get('get-all-games-by-genre')
      ->executeOnce([$genre], 3)
      ->fetch()
    ;
  }

  public function addGenres(string $game, array $genres): void {
    $this->verify();

    foreach ($genres as $genre) {
      $this->addPair($game, $genre);
    }
  }

  public function addGames(string $genre, array $games): void {
    $this->verify();

    foreach ($games as $game) {
      $this->addPair($game, $genre);
    }
  }

  public function deleteGenres(string $game, array $genres): void {
    $this->verify();

    foreach ($genres as $genre) {
      $this->deletePair($game, $genre);
    }
  }

  public function deleteGames(string $genre, array $games): void {
    $this->verify();

    foreach ($games as $game) {
      $this->deletePair($game, $genre);
    }
  }

  public function clearGenres(string $game): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('remove-all-genres-from-game')
      ->executeOnce([$game])
    ;
  }

  public function clearGames(string $genre): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('remove-all-games-from-genre')
      ->executeOnce([$genre])
    ;
  }

  public function setGenres(string $game, array $genres): void {
    $this->verify();
    $this->clearGenres($game);
    $this->addGenres($game, $genres);
  }

  public function setGames(string $genre, array $games): void {
    $this->verify();
    $this->clearGames($genre);
    $this->addGames($genre, $games);
  }

  public function list(): array {
    $list = $this
      ->get('db-query-set')
      ->get('list-game-genre-pairs')
      ->executeOnce([], 3 + 2)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'game-id' => $row[0],
          'genre-id' => $row[1],
          'game-name' => $row[2],
          'game-description' => $row[3],
          'genre-name' => $row[4],
        ]);
      },
      $list
    );
  }

  public function clear(): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('clear-game-genre-pairs')
      ->executeOnce([])
    ;
  }

  public function reset(): void {
    $this->verify();
    $this->clear();
  }
}
?>
