<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../lib/utils.php';

class Login extends RawDataContainer {
  public function verify(): LoginInfo {
    $postData = $this->get('post-data');
    $cookie = $this->get('cookie');

    if ($postData->getDefault('logged-in', 'off') === 'on') {
      $cookie->assign([
        'logged-in' => 'on',
        'username' => $postData->get('username'),
        'password' => $postData->get('password'),
      ])->update();

      $postData->without([
        'logged-in',
        'username',
        'password',
      ])->update($_POST);

      $urlQuery->redirect();
    }

    if ($cookie->getDefault('logged-in', 'off') === 'on') {
      [
        'username' => $username,
        'password' => $password,
      ] = $cookie->getData();

      return new LoginInfo([
        'logged-in' => 'on',
        'username' => $username,
        'password' => $password,
      ]);
    }

    return new LoginInfo(['logged-in' => 'off']);
  }
}

class LoginInfo extends RawDataContainer {
  public function isLoggedIn(): bool {
    return $this->getDefault('logged-in', 'off') === 'on';
  }

  public function username(): string {
    return $this->getDefault('username', '');
  }

  public function password(): string {
    return $this->getDefault('password', '');
  }
}
?>
