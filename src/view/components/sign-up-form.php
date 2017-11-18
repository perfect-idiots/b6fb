<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/labeled-input.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class SignUpForm extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('form', [
      HtmlElement::emmetTop('.input-container', [
        PlainLabeledInput::text('fullname', 'Họ và Tên'),
        PlainLabeledInput::text('username', 'Tên tài khoản'),
        SecretLabeledInput::text('password', 'Mật khẩu'),
        SecretLabeledInput::text('re-password', 'Nhập lại mật khẩu'),
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
    ]);
  }
}
?>
