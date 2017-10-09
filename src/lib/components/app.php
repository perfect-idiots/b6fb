<?php
require_once __DIR__ . '/head-section.php';
require_once __DIR__ . '/body-section.php';

class App implements Component {
  public function render(): Component {
    return HTMLElement::create('html', array(
      new HeadSection(),
      new BodySection()
    ));
  }
}
?>
