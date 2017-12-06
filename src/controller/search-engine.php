<?php
require_once __DIR__ . '/../lib/utils.php';

class SearchEngine extends RawDataContainer {
  static protected function requiredFieldSchema(): array {
    return [
      'db-query-set' => 'DatabaseQuerySet',
    ];
  }

  public function searchGames(string $search): array {
    $dbResponse = $this
      ->get('db-query-set')
      ->get('search-games')
      ->executeOnce([$search], 3)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'name' => $row[1],
          'description' => $row[2],
        ]);
      },
      $dbResponse
    );
  }
}
?>
