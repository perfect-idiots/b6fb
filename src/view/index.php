<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/components/app.php';

class Page extends DataContainer {
  public function render(): string {
    $renderer = new Renderer(false);
    $app = new App($this->getData());
    $html = $renderer->render($app);
    return "<!DOCTYPE html>\n$html\n";
  }
}
?>
