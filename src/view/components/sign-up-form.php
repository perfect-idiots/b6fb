<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/instructed-input.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/hidden-input.php';
require_once __DIR__ . '/../../lib/utils.php';

class SignUpForm extends RawDataContainer implements Component {
  public function render(): Component {
    $sessionData = $this->get('session')->getData();

    return HtmlElement::create('form', [
      'method' => 'POST',
      'action' => '.',
      HtmlElement::emmetTop('.input-container', [
        new SignUpField(
          'PlainInstructedInput',
          'fullname',
          'Họ và Tên',
          'Họ tên đầy đủ',
          $sessionData
        ),
        new SignUpField(
          'PlainInstructedInput',
          'username',
          'Tên đăng nhập',
          'Tên tài khoản',
          $sessionData
        ),
        new SignUpField(
          'SecretInstructedInput',
          'password',
          'Mật khẩu',
          'Hơn 6 ký tự, bao gồm kí tự số, chữ thường, chữ hoa',
          $sessionData
        ),
        new SignUpField(
          'SecretInstructedInput',
          're-password',
          'Nhập lại mật khẩu',
          'Nhập lại mật khẩu vừa nhập',
          $sessionData
        ),
      ]),
      HtmlElement::emmetTop('.button-container', [
        new PrimaryButton([
          'type' => 'submit',
          'Tạo tài khoản',
        ]),
        new PrimaryButton([
          'type' => 'reset',
          'Xóa',
        ]),
      ]),
      new HiddenInputSet(['signed-up' => 'on']),
    ]);
  }

  static private function error(array $data, string $field): string {}
}

class SignUpField implements Component {
  private $component, $field, $label, $instruction, $data;

  public function __construct(string $component, string $field, string $label, string $instruction, array $data) {
    $this->component = $component;
    $this->field = $field;
    $this->label = $label;
    $this->instruction = $instruction;
    $this->data = $data;
  }

  public function render(): Component {
    $alert = new SignUpAlert($this->data, $this->field);
    $component = $this->component;

    $child = $component::text(
      $this->field,
      $this->label,
      $this->instruction,
      $alert
    );

    return HtmlElement::create('div', [
      'classes' => $alert->isMarked() ? ['invalid'] : [],
      $child
    ]);
  }
}

class SignUpAlert implements Component {
  private $data, $field;
  const MSGMAP = [
    'empty' => 'Không được để trống',
    'taken' => 'Không có sẵn',
    'insufficient-length' => 'Quá ngắn',
    'mismatch' => 'Không khớp',
  ];

  public function __construct(array $data, string $field) {
    $this->data = new RawDataContainer($data);
    $this->field = $field;
    $error = $this->data->getDefault('sign-up-succeed', 'on') === 'off';
    $field = $this->data->getDefault('sign-up-error-field', '') === $this->field;
    $this->marked = $error && $field;
  }

  public function render(): Component {
    return $this->isMarked()
      ? HtmlElement::emmetTop('span.error-message', $this->message())
      : new TextNode('')
    ;
  }

  public function isMarked(): bool {
    return $this->marked;
  }

  private function message(): string {
    return self::MSGMAP[$this->data->get('sign-up-error-reason')];
  }
}
?>
