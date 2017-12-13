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
    $dbResult = $this
      ->get('db-query-set')
      ->get('list-comments')
      ->executeOnce([], 9)
      ->fetch()
    ;

    return $this->group(array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'author-id' => $row[1],
          'game-id' => $row[2],
          'parent-comment-id' => $row[3],
          'date' => $row[4],
          'hidden' => (bool) $row[5],
          'content' => $row[6],
          'author-fullname' => $row[7],
          'game-name' => $row[8],
        ]);
      },
      $dbResult
    ));
  }

  public function listByUser(string $id, bool $filter, bool $hidden = false): array {
    $dbResult = $this
      ->get('db-query-set')
      ->get('get-all-comments-by-user')
      ->executeOnce([$id, (int) $filter, (int) $hidden], 9)
      ->fetch()
    ;

    return $this->group(array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'game-id' => $row[1],
          'parent-comment-id' => $row[2],
          'date' => $row[3],
          'hidden' => (bool) $row[4],
          'content' => $row[5],
          'author-fullname' => $row[6],
          'game-name' => $row[7],
          'author-id' => $row[8],
        ]);
      },
      $dbResult
    ));
  }

  public function listByGame(string $id, bool $filter, bool $hidden = false): array {
    $dbResult = $this
      ->get('db-query-set')
      ->get('get-all-comments-by-game')
      ->executeOnce([$id, (int) $filter, (int) $hidden], 9)
      ->fetch()
    ;

    return $this->group(array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'author-id' => $row[1],
          'parent-comment-id' => $row[2],
          'date' => $row[3],
          'hidden' => (bool) $row[4],
          'content' => $row[5],
          'author-fullname' => $row[6],
          'game-id' => $row[7],
          'game-name' => $row[8],
        ]);
      },
      $dbResult
    ));
  }

  public function getUnknownCommentsByParent(array $knownComments, int $parent, bool $filter = false, bool $hidden = false): array {
    $serialized = implode(',', $knownComments);

    $dbResult = $this
      ->get('db-query-set')
      ->get('get-unknown-replying-comments')
      ->executeOnce([$serialized, $parent, (int) $filter, (int) $hidden], 9)
      ->fetch()
    ;

    return $this->group(array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'author-id' => $row[1],
          'game-id' => $row[2],
          'parent-comment-id' => $row[3],
          'date' => $row[4],
          'hidden' => (bool) $row[5],
          'content' => $row[6],
          'author-fullname' => $row[7],
          'game-name' => $row[8],
        ]);
      },
      $dbResult
    ));
  }

  private function group(array $list): array {
    $surface = filterArrayConcrete($list, function (array $row) {
      return $row['parent-comment-id'] === null;
    });

    return array_merge($list, [
      'all' => $list,
      'surface' => $surface,
      'groups' => array_map(
        function (array $top) use($list) {
          $replies = filterArrayConcrete($list, function (array $reply) use($top) {
            return $reply['parent-comment-id'] === $top['id'];
          });

          $all = array_merge([$top], $replies);

          return array_merge($all, [
            'all' => $all,
            'top' => $top,
            'replies' => $replies,
          ]);
        },
        $surface
      ),
    ]);
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
