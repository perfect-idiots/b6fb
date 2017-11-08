<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/components/app.php';

abstract class Page extends RawDataContainer {
  abstract protected function component(): Component;

  public function render(): string {
    $renderer = new Renderer(false);
    $component = $this->component();
    $html = $renderer->render($component);
    return "<!DOCTYPE html>\n$html\n";
  }
}

class MainPage extends Page {
  protected function component(): Component {
    return new App($this->getData());
  }
}
?>
