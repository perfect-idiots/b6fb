<?php
require_once __DIR__ . '/../lib/utils.php';

class SearchEngine extends RawDataContainer {
  static protected function requiredFieldSchema(): array {
    return [
      'db-query-set' => 'DatabaseQuerySet',
    ];
  }

  public function searchGames(string $search): array {
    return $this
      ->get('db-query-set')
      ->get('search-games')
      ->executeOnce([$search], 3)
      ->fetch()
    ;
  }
}
?>
