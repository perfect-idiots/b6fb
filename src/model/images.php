<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/url-query.php';

class ImageUrlQuery extends UrlQuery {
  public function __construct(array $data) {
    parent::__construct(array_merge($data, ['type' => 'image']));
  }
}

abstract class ThemedImageUrlQuery extends ImageUrlQuery {
  abstract static protected function name(): string;
  abstract static protected function ext(): string;
  abstract static protected function mime(): string;

  static public function theme(string $theme): self {
    return new static([
      'name' => implode('.', [static::name(), $theme, static::ext()]),
      'mime' => static::mime(),
    ]);
  }
}

abstract class ThemedSvgImage extends ThemedImageUrlQuery {
  static protected function ext(): string {
    return 'svg';
  }

  static protected function mime(): string {
    return 'image/svg+xml';
  }
}

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
