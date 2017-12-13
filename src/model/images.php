<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/url-query.php';

class ImageUrlQuery extends UrlQuery {
  public function __construct(array $data) {
    parent::__construct(array_merge($data, static::defaultFields()));
  }

  protected function defaultFields(): array {
    return [
      'type' => 'file',
      'purpose' => 'ui',
    ];
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

class MenuIcon extends SvgImage {
  protected function name(): string {
    return 'menu.svg';
  }
}

abstract class StarIcon extends SvgImage {
  abstract protected function suffix(): string;

  protected function name(): string {
    return 'star-' . $this->suffix() . '.svg';
  }
}

class StarFillIcon extends StarIcon {
  protected function suffix(): string {
    return 'fill';
  }
}

class StarStrokeWhiteIcon extends StarIcon {
  protected function suffix(): string {
    return 'stroke-white';
  }
}

class StarStrokeBlackIcon extends StarIcon {
  protected function suffix(): string {
    return 'stroke-black';
  }
}

abstract class DefaultAvatarImage extends SvgImage {
  abstract protected function suffix(): string;

  protected function name(): string {
    return 'default-avatar-' . $this->suffix() . '.svg';
  }
}

class DefaultAvatarWhiteImage extends DefaultAvatarImage {
  protected function suffix(): string {
    return 'white';
  }
}

class DefaultAvatarBlackImage extends DefaultAvatarImage {
  protected function suffix(): string {
    return 'black';
  }
}

class GamepadImage extends SvgImage {
  protected function name(): string {
    return 'gamepad.svg';
  }
}

class MultiUsersImage extends SvgImage {
  protected function name(): string {
    return 'multi-users.svg';
  }
}

class ImageSet extends LazyLoadedDataContainer {
  protected function load(): array {
    $theme = $this->param['name'];

    $classes = [
      'SearchIcon',
      'NightModeIcon',
      'MenuIcon',
      'DefaultAvatarWhiteImage',
      'DefaultAvatarBlackImage',
      'GamepadImage',
      'MultiUsersImage',
      'StarFillIcon',
      'StarStrokeWhiteIcon',
      'StarStrokeBlackIcon',
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
