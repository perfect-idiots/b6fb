<?php
require_once __DIR__ . '/security.php';

class CommentManager extends LoginDoubleChecker {
  public function delete(int $id): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('delete-comment')
      ->executeOnce([$id])
    ;
  }

  public function hide(int $id): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('hide-comment')
      ->executeOnce([$id])
    ;
  }

  public function reveal(int $id): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('reveal-comment')
      ->executeOnce([$id])
    ;
  }

  public function list(): array {
    $this->verify();

    $dbResult = $this
      ->get('db-query-set')
      ->get('list-comments')
      ->executeOnce([], 6)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'author-id' => $row[1],
          'game-id' => $row[2],
          'parent-comment-id' => $row[3],
          'date' => $row[4],
          'hidden' => (bool) $row[5],
          'author-fullname' => $row[6],
          'game-name' => $row[7],
        ]);
      },
      $dbResult
    );
  }

  public function listByUser(string $id, bool $filter, bool $hidden): array {
    $this->verify();

    $dbResult = $this
      ->get('db-query-set')
      ->get('get-all-comments-by-user')
      ->executeOnce([$id, (int) $filter, (int) $hidden], 6)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'game-id' => $row[1],
          'parent-comment-id' => $row[2],
          'date' => $row[3],
          'hidden' => (bool) $row[4],
          'author-fullname' => $row[5],
          'game-name' => $row[6],
          'author-id' => $row[7],
        ]);
      },
      $dbResult
    );
  }

  public function listByGame(string $id, bool $filter, bool $hidden): array {
    $this->verify();

    $dbResult = $this
      ->get('db-query-set')
      ->get('get-all-comments-by-game')
      ->executeOnce([$id, (int) $filter, (int) $hidden], 6)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'author-id' => $row[1],
          'parent-comment-id' => $row[2],
          'date' => $row[3],
          'hidden' => (bool) $row[4],
          'author-fullname' => $row[5],
          'game-id' => $row[6],
          'game-name' => $row[7],
        ]);
      },
      $dbResult
    );
  }

  public function clear(): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('clear-comments')
      ->executeOnce([])
    ;
  }

  public function reset(): void {
    $this->verify();
    $this->clear();
  }
}
?>
