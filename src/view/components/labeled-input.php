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

  static public function text(string $id, string $label): self {
    return new static([
      'id' => $id,
      'label' => $label,
      'input-attr' => static::textInputAttr(),
      'label-attr' => static::textLabelAttr(),
    ]);
  }

  static protected function textLabelAttr(): array {
    return [];
  }

  static protected function textInputAttr(): array {
    return [];
  }
}

class RequiredLabeledInput extends LabeledInput {
  static protected function textInputAttr(): array {
    return ['required' => true];
  }
}
?>
