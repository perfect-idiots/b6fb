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
      'action' => '',
      HtmlElement::emmetTop('.input-container', [
        PlainInstructedInput::text(
          'fullname',
          'Họ và Tên',
          'Họ tên đầy đủ',
          new SignUpAlert(
            $sessionData,
            'fullname'
          )
        ),
        PlainInstructedInput::text(
          'username',
          'Tên tài khoản',
          'Tên đăng nhập',
          new SignUpAlert(
            $sessionData,
            'username'
          )
        ),
        SecretInstructedInput::text(
          'password',
          'Mật khẩu',
          'Hơn 6 ký tự, bao gồm kí tự số, chữ thường, chữ hoa',
          new SignUpAlert(
            $sessionData,
            'password'
          )
        ),
        SecretInstructedInput::text(
          're-password',
          'Nhập lại mật khẩu',
          'Nhập lại mật khẩu vừa nhập',
          new SignUpAlert(
            $sessionData,
            're-password'
          )
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
  }

  public function render(): Component {
    $error = $this->data->getDefault('sign-up-succeed', 'on') === 'off';
    $field = $this->data->getDefault('sign-up-error-field', '') === $this->field;
    $print = $error && $field;

    return $print
      ? HtmlElement::emmetTop('span.error-message', $this->message())
      : new TextNode('')
    ;
  }

  private function message(): string {
    return self::MSGMAP[$this->data->get('sign-up-error-reason')];
  }
}
?>
