<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/header-section.php';
require_once __DIR__ . '/main-section.php';
require_once __DIR__ . '/footer-section.php';

class BodySection implements Component {
  public function render(): Component {
    return HtmlElement::create('body', array(
      new HeaderSection(),
      new MainSection(),
      new FooterSection()
    ));
  }
}
?>
