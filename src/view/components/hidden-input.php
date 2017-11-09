<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class HiddenInput implements Component {
  private $name, $value, $attr;

  public function __construct(string $name, string $value, array $attr = []) {
    $this->name = $name;
    $this->value = $value;
    $this->attr = $attr;
  }

  public function render(): Component {
    return HtmlElement::create('input', array_merge(
      [
        'type' => 'hidden',
        'hidden' => true,
        'name' => $this->name,
        'value' => $this->value,
      ],
      $this->attr
    ));
  }
}

class HiddenInputSet extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();
    $children = [];

    foreach($data as $name => $value) {
      array_push($children, new HiddenInput($name, $value));
    }

    return HtmlElement::create('div', array_merge(
      ['hidden' => true],
      $children
    ));
  }
}
?>
