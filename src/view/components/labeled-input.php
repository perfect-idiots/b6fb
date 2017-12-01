<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class LabeledInput extends RawDataContainer implements Component {
  public function render(): Component {
    $tagName = $this->getDefault('tag', $this->defaultTagName());
    $id = $this->getDefault('id', null);
    $type = $this->getDefault('type', null);
    $label = $this->getDefault('label', '');
    $value = $this->getDefault('value', static::defaultValue());
    $valueField = $this->valueField();

    $labelAttr = array_merge(
      static::defaultLabelAttr(),
      $this->getDefault('label-attr', [])
    );

    $inputAttr = array_merge(
      static::defaultInputAttr(),
      $this->getDefault('input-attr', []),
      $value
        ? ($valueField
          ? [$valueField => $value]
          : [$value]
        )
        : []
    );

    $labelElement = HtmlElement::create('label', array_merge(
      [$label],
      $labelAttr,
      $id ? ['for' => $id] : []
    ));

    $inputElement = HtmlElement::create($tagName, array_merge($inputAttr, array_merge(
      $id ? ['id' => $id, 'name' => $id] : [],
      $type ? ['type' => $type] : []
    )));

    return HtmlElement::create(
      'div',
      static::reversedOrder()
        ? [$inputElement, $labelElement]
        : [$labelElement, $inputElement]
    );
  }

  protected function reversedOrder(): bool {
    return false;
  }

  protected function defaultTagName(): string {
    return 'input';
  }

  protected function valueField(): string {
    return 'value';
  }

  static protected function defaultValue() {
    return '';
  }

  static protected function defaultLabelAttr(): array {
    return [];
  }

  static protected function defaultInputAttr(): array {
    return [];
  }

  static public function text(string $id, string $label, $value = ''): self {
    return new static([
      'id' => $id,
      'label' => $label,
      'value' => $value,
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

class ReversedLabeledInput extends LabeledInput {
  protected function reversedOrder(): bool {
    return true;
  }
}

abstract class LabeledCheckboxRadio extends ReversedLabeledInput {
  protected function valueField(): string {
    return 'checked';
  }

  static protected function defaultValue() {
    return false;
  }
}

class LabeledCheckbox extends LabeledCheckboxRadio {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['type' => 'checkbox']);
  }
}

class LabeledRadio extends LabeledCheckboxRadio {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['type' => 'radio']);
  }
}

class RequiredLabeledInput extends LabeledInput {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['required' => true]);
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

class LabeledTextArea extends LabeledInput {
  protected function defaultTagName(): string {
    return 'textarea';
  }

  protected function valueField(): string {
    return '';
  }
}

class LabeledFileInput extends LabeledInput {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['type' => 'file']);
  }
}

class RequiredTextArea extends RequiredLabeledInput {
  protected function defaultTagName(): string {
    return 'textarea';
  }

  protected function valueField(): string {
    return '';
  }
}

class RequiredFileInput extends RequiredLabeledInput {
  static protected function defaultInputAttr(): array {
    return array_merge(parent::defaultInputAttr(), ['type' => 'file']);
  }
}
?>
