<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/labeled-input.php';
require_once __DIR__ . '/../../lib/utils.php';

abstract class InstructedInput extends RawDataContainer implements Component {
  abstract protected function createLabeledInput(array $attr): LabeledInput;

  public function render(): Component {
    $data = $this->getData();
    $instruction = $this->getDefault('instruction', '');
    $alert = $this->getDefault('alert', []);
    $inputAttr = $this->getDefault('input-attr', []);

    return HtmlElement::create('div', [
      $this->createLabeledInput(array_merge($data, [
        'input-attr' => array_merge($inputAttr, [
          'placeholder' => $instruction,
        ]),
      ])),
      new InstructedInputAlert(
        gettype($alert) === 'array' ? $alert : [$alert]
      ),
    ]);
  }

  static public function text(string $id, string $label, string $instruction, string $alert): self {
    return static::instance([
      'id' => $id,
      'label' => $label,
      'instruction' => $instruction,
      'alert' => $alert,
    ]);
  }
}

class RequiredInstructedInput extends InstructedInput {
  protected function createLabeledInput(array $attr): LabeledInput {
    return new RequiredLabeledInput($attr);
  }
}

class PlainInstructedInput extends InstructedInput {
  protected function createLabeledInput(array $attr): LabeledInput {
    return new PlainLabeledInput($attr);
  }
}

class SecretInstructedInput extends InstructedInput {
  protected function createLabeledInput(array $attr): LabeledInput {
    return new SecretLabeledInput($attr);
  }
}

class InstructedInputAlert extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('div', $this->getData());
  }
}
?>
