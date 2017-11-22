<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/footer-section.php';

class MainSection extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();
    $page = $data['page'];
    return HtmlElement::create('main', [
      'id' => 'main-section',
      "Current Page:",
      HtmlElement::emmetBottom('i>u>code', $page),
      new FooterSection(),
    ]);
  }
}
?>
