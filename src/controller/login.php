<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../lib/utils.php';

class Login extends RawDataContainer {
  public function verify(): LoginInfo {
    $postData = $this->get('post-data');
    $cookie = $this->get('cookie');
    $urlQuery = $this->get('url-query');
    $isAdmin = $this->getDefault('is-admin', false);
    $ckprfx = $isAdmin ? 'admin-' : '';
    $ckloggedin = $ckprfx . 'logged-in';
    $ckusername = $ckprfx . 'username';
    $ckpassword = $ckprfx . 'password';

    if ($postData->getDefault('logged-in', 'off') === 'on') {
      $cookie->assign([
        $ckloggedin => 'on',
        $ckusername => $postData->get('username'),
        $ckpassword => $postData->get('password'),
      ])->update();

      $postData->without([
        'logged-in',
        'username',
        'password',
      ])->update($_POST);

      $urlQuery->redirect();
    }

    if ($cookie->getDefault($ckloggedin, 'off') === 'on') {
      [
        $ckusername => $username,
        $ckpassword => $password,
      ] = $cookie->getData();

      return new LoginInfo([
        'logged-in' => true,
        'username' => $username,
        'password' => $password,
      ]);
    }

    return new LoginInfo(['logged-in' => false]);
  }
}

class LoginInfo extends RawDataContainer {
  public function isLoggedIn(): bool {
    return $this->get('logged-in');
  }

  public function username(): string {
    return $this->getDefault('username', '');
  }

  public function password(): string {
    return $this->getDefault('password', '');
  }
}
?>
