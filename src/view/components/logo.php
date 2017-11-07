<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/anchor.php';
require_once __DIR__ . '/../../lib/utils.php';

class Logo extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('h1', array(
      'id' => 'main-logo',
      'classes' => array('logo'),
      Anchor::withoutAttributes('?page=index', $this->get('title')),
    ));
  }
}
?>
