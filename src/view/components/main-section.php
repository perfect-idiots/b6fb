<?php
require_once __DIR__ . '/base.php';

class MainSection extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();
    $page = $data['page'];
    return HtmlElement::create('main', [
      'id' => 'main-section',
      "Current Page:",
      HtmlElement::nested(['i', 'u', 'code'], $page),
      HtmlElement::emmetBottom('a-b-c>d-e-f>g-h-i', 'hello'),
    ]);
  }
}
?>
