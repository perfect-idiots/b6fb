<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class LabeledInput extends RawDataContainer implements Component {
  public function render(): Component {
    $tagName = $this->getDefault('tag', $this->defaultTagName());
    $id = $this->getDefault('id', null);
    $type = $this->getDefault('type', null);
    $label = $this->getDefault('label', '');

    $labelAttr = array_merge(
      static::defaultLabelAttr(),
      $this->getDefault('label-attr', [])
    );

    $inputAttr = array_merge(
      static::defaultInputAttr(),
      $this->getDefault('input-attr', [])
    );


    return HtmlElement::create('div', [
      HtmlElement::create('label', array_merge(
        [$label],
        $labelAttr,
        $id ? ['for' => $id] : []
      )),
      HtmlElement::create($tagName, array_merge($inputAttr, array_merge(
        $id ? ['id' => $id, 'name' => $id] : [],
        $type ? ['type' => $type] : []
      ))),
    ]);
  }

  protected function defaultTagName(): string {
    return 'input';
  }

  static protected function defaultLabelAttr(): array {
    return [];
  }

  static protected function defaultInputAttr(): array {
    return [];
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
  static protected function defaultInputAttr(): array {
    return ['required' => true];
  }
}

class PlainLabeledInput extends RequiredLabeledInput {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['type' => 'text']);
  }
}

class SecretLabeledInput extends RequiredLabeledInput {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['type' => 'password']);
  }
}

class LabeledTextArea extends RequiredLabeledInput {
  protected function defaultTagName(): string {
    return 'textarea';
  }
}

class LabeledFileInput extends RequiredLabeledInput {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['type' => 'file']);
  }
}
?>
