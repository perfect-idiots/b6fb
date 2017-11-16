<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class LabeledInput extends RawDataContainer implements Component {
  public function render(): Component {
    $tagName = $this->getDefault('tag', 'input');
    $id = $this->getDefault('id', '');
    $type = $this->getDefault('type', 'text');
    $label = $this->getDefault('label', '');
    $inputAttr = $this->getDefault('input-attr', []);
    $labelAttr = $this->getDefault('label-attr', []);

    return HtmlElement::create('div', [
      HtmlElement::create('label', array_merge($labelAttr, [
        'for' => $id,
      ])),
      HtmlElement::create($tagName, array_merge($inputAttr, [
        'id' => $id,
        'type' => $type,
      ])),
    ]);
  }

  static public function text(string $id, string $label, string $placeholder = ''): self {
    return new LabeledInput([
      'id' => $id,
      'label' => $label,
      'input-attr' => [
        'placeholder' => $placeholder,
      ],
    ]);
  }
}
?>
