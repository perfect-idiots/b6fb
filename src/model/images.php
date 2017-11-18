<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/url-query.php';

class ImageUrlQuery extends UrlQuery {
  public function __construct(array $data) {
    parent::__construct(array_merge($data, ['type' => 'image']));
  }
}

abstract class FixedImageUrlQuery extends ImageUrlQuery {
  abstract protected function mime(): string;
  abstract protected function name(): string;

  public function __construct(array $data) {
    parent::__construct(array_merge($data, [
      'name' => static::name(),
      'mime' => static::mime(),
    ]));
  }

  static public function build(array $data = []): self {
    return new static($data);
  }
}

abstract class SvgImage extends FixedImageUrlQuery {
  protected function mime(): string {
    return 'image/svg+xml';
  }
}

class SearchIcon extends SvgImage {
  protected function name(): string {
    return 'search.svg';
  }
}

class NightModeIcon extends SvgImage {
  protected function name(): string {
    return 'night-mode.svg';
  }
}

class DefaultAvatarImage extends SvgImage {
  protected function name(): string {
    return 'default-avatar.svg';
  }
}

class ImageSet extends LazyLoadedDataContainer {
  protected function load(): array {
    $theme = $this->param['name'];

    $classes = [
      'SearchIcon',
      'NightModeIcon',
      'DefaultAvatarImage',
    ];

    $result = [];
    foreach ($classes as $class) {
      $key = CaseConverter::fromPascalCase($class)->toKebabCase();
      $result[$key] = $class::build()->getUrlQuery();
    }

    return $result;
  }
}
?>
