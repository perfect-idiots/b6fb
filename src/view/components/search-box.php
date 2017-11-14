<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/hidden-input.php';

class SearchBox extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('form', [
      'id' => 'search-box',
      'method' => 'GET',
      'action' => '.',
      HtmlElement::create('input', [
        'id' => 'search-box-input',
        'name' => 'search',
        'type' => 'text',
        'placeholder' => 'Search',
      ]),
      HtmlElement::emmet('button#search-box-button'),
      new HiddenInputSet(
        $this->get('url-query')->except('search')->getData()
      ),
    ]);
  }
}
?>
