<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/labeled-input.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class LoginForm extends RawDataContainer implements Component {
  public function render(): Component {
    $action = $this->getDefault('action', '.');
    $signup = $this->getDefault('sign-up', '.');

    return HtmlElement::emmetTop('form', [
      'action' => $action,
      HtmlElement::emmetTop('.input-container', [
        LabeledInput::text('username', 'Tên đăng nhập'),
        LabeledInput::text('password', 'Mật khẩu'),
      ]),
      HtmlElement::emmetTop('.button-container', [
        new PrimaryButton([
          'type' => 'submit',
          'Đăng nhập',
        ]),
        new PrimaryButton([
          'type' => 'reset',
          'Xóa',
        ]),
        HtmlElement::emmetTop('a#sign-up-anchor.sign-up', [
          'href' => $signup,
          'Tạo tài khoản',
        ]),
      ]),
    ]);
  }
}
?>
