<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/render.php';

abstract class Prerendered implements Component {
  protected $renderer, $component;
  abstract static protected function getRenderParam(): bool;

  public function __construct(Component $component) {
    $this->component = $component;
    $this->renderer = new Renderer(static::getRenderParam());
  }

  public function render(): Component {
    return new UnescapedText($this->renderer->render($this->component));
  }
}

class Prettified extends Prerendered {
  static protected function getRenderParam(): bool {
    return false;
  }
}

class Minified extends Prerendered {
  static protected function getRenderParam(): bool {
    return true;
  }
}
?>
