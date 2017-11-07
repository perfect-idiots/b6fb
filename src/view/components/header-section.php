<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/../../lib/utils.php';

class HeaderSection extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('header', array(
      'id' => 'main-header',
      'classes' => array('header'),
      new Logo($this->getData()),
    ));
  }
}
?>
