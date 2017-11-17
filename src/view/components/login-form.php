<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/labeled-input.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/hidden-input.php';
require_once __DIR__ . '/../../lib/utils.php';

class LoginForm extends RawDataContainer implements Component {
  public function render(): Component {
    $action = $this->getDefault('action', '.');
    $signup = $this->getDefault('sign-up', null);
    $hidden = $this->getDefault('hidden-values', []);

    return HtmlElement::emmetTop('form', [
      'action' => $action,
      'method' => 'POST',
      HtmlElement::emmetTop('.input-container', [
        PlainLabeledInput::text('username', 'Tên đăng nhập', '', true),
        SecretLabeledInput::text('password', 'Mật khẩu', '', true),
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
        $signup === null
          ? ''
          : HtmlElement::emmetTop('a#sign-up-anchor.sign-up', [
            'href' => $signup,
            'Tạo tài khoản',
          ])
        ,
      ]),
      new HiddenInputSet(array_merge($hidden, ['logged-in' => 'on'])),
    ]);
  }
}
?>
