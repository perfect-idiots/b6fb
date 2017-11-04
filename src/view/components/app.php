<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/head-section.php';
require_once __DIR__ . '/body-section.php';

class App implements Component {
  public function render(): Component {
    return HtmlElement::create('html', array(
      new HeadSection(),
      new BodySection()
    ));
  }
}
?>
