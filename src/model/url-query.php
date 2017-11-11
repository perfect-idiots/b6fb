<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../view/components/redirect-page.php';

class UrlQuery extends RawDataContainer {
  static public function from(array $data): self {
    return new static($data);
  }

  public function getUrlQuery(): string {
    return '?' . http_build_query($this->getData());
  }

  public function redirect(string $prefix = ''): void {
    $location = $prefix . $this->getUrlQuery();
    header("Location: $location");
    $renderer = new Renderer(true);
    $page = new RedirectPage($location);
    echo $renderer->render($page);
  }
}

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

  static protected function name(): string {
    return 'image/xml+svg';
  }
}
?>
