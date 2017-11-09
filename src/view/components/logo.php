<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/anchor.php';
require_once __DIR__ . '/../../lib/utils.php';

class Logo extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('h1', [
      'id' => 'main-logo',
      'classes' => ['logo'],
      Anchor::withoutAttributes(
        $this->get('url-query')->set('page', 'index')->getUrlQuery(),
        $this->get('title')
      ),
    ]);
  }
}
?>
