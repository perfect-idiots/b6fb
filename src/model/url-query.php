<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../view/components/redirect-page.php';

class UrlQuery extends RawDataContainer {
  private $prefix, $separator;

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
?>
