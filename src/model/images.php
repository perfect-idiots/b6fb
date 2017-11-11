<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/url-query.php';

class SearchIcon extends ThemedSvgImage {
  static protected function name(): string {
    return 'search';
  }
}

class ImageSet extends LazyLoadedDataContainer {
  protected function load(): array {
    $theme = $this->param['name'];

    $classes = [
      'SearchIcon',
    ];

    $result = [];
    foreach ($classes as $class) {
      $key = CaseConverter::fromPascalCase($class)->toKebabCase();
      $result[$key] = $class::theme($theme)->getUrlQuery();
    }

    return $result;
  }
}
?>
