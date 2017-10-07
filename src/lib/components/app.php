<?php
require_once __DIR__ . '/head.php';
require_once __DIR__ . '/body.php';

class App implements Component {
  public function render(): Component {
    return new Element('html', array(), array(
      new Head(),
      new Body()
    ));
  }
}
?>
