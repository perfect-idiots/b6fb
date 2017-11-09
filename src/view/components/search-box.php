<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/hidden-input.php';

class SearchBox extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('form', array(
      'id' => 'search-box',
      'method' => 'GET',
      'action' => '.',
      HtmlElement::create('input', array(
        'id' => 'search-box-input',
        'name' => 'search',
        'type' => 'text',
        'placeholder' => 'Search',
      )),
      HtmlElement::create('button', array(
        'id' => 'search-box-button',
        'Search'
      )),
      new HiddenInputSet(
        $this->get('url-query')->except('search')->getData()
      ),
    ));
  }
}
?>
