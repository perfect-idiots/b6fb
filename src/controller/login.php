<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../lib/utils.php';

class Login extends RawDataContainer {
  public function verify(): LoginInfo {
    $postData = $this->get('post-data');
    $cookie = $this->get('cookie');
    $session = $this->get('session');
    $urlQuery = $this->get('url-query');
    $isAdmin = $this->getDefault('is-admin', false);
    $dbQuerySet = $this->get('db-query-set');
    $ckprfx = self::getCkPrfx($isAdmin);
    $ckloggedin = $ckprfx . 'logged-in';
    $ckusername = $ckprfx . 'username';
    $ckpassword = $ckprfx . 'password';
    $cksessionid = $ckprfx . 'session-id';

    if ($postData->getDefault('logged-in', 'off') === 'on') {
      [
        'sid' => $sid,
        'sidhash' => $sidhash,
      ] = self::newSessionId();

      $session->set($cksessionid, $sid)->update();

      $cookie->assign([
        $ckloggedin => 'on',
        $ckusername => $postData->get('username'),
        $ckpassword => $postData->get('password'),
        $cksessionid => $sidhash,
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

      return self::checkLogin([
        'logged-in' => true,
        'cookie' => $cookie,
        'session' => $session,
        'username' => $username,
        'password' => $password,
        'is-admin' => $isAdmin,
        'db-query-set' => $dbQuerySet,
      ]);
    }

    return new LoginInfo(['logged-in' => false]);
  }

  static protected function requiredFieldSchema(): array {
    return [
      'cookie' => 'Cookie',
      'session' => 'Session',
      'db-query-set' => 'DatabaseQuerySet',
    ];
  }

  static public function checkLogin(array $param): LoginInfo {
    if (!$param['logged-in']) {
      return new LoginInfo([
        'logged-in' => false,
        'error-reason' => 'invalid-username',
      ]);
    }

    [
      'cookie' => $cookie,
      'session' => $session,
      'username' => $username,
      'password' => $password,
      'is-admin' => $isAdmin,
      'db-query-set' => $dbQuerySet,
    ] = $param;

    // if (self::checkSessionAuth($cookie, $session, $isAdmin)) {
    //   return new LoginInfo([
    //     'logged-in' => true,
    //     'is-admin' => $isAdmin,
    //     'username' => $username,
    //     'password' => $password,
    //   ]);
    // }

    [
      'sid' => $sid,
      'sidhash' => $sidhash,
    ] = self::newSessionId();

    $query = $dbQuerySet->get($isAdmin ? 'admin-password' : 'user-password');
    $dbResult = $query->executeOnce([$username], 1)->fetch();

    if (!sizeof($dbResult)) {
      return new LoginInfo([
        'logged-in' => false,
        'error-reason' => 'invalid-username',
      ]);
    }

    [[$hash]] = $dbResult;

    if (!password_verify($password, $hash)) {
      return new LoginInfo([
        'logged-in' => false,
        'error-reason' => 'invalid-password',
      ]);
    }

    return new LoginInfo([
      'logged-in' => true,
      'is-admin' => $isAdmin,
      'username' => $username,
      'password' => $password,
      'sid' => $sid,
      'sidhash' => $sidhash,
    ]);
  }

  static private function newSessionId(): array {
    $sid = bin2hex(random_bytes(32));
    $sidhash = hash('sha256', $sid);

    return [
      'sid' => $sid,
      'sidhash' => $sidhash,
    ];
  }

  static private function getCkPrfx(bool $isAdmin): string {
    return $isAdmin ? 'admin-' : '';
  }

  static private function checkSessionAuth(Cookie $cookie, Session $session, bool $isAdmin): bool {
    $cksessionid = self::getCkPrfx($isAdmin) . 'session-id';
    if (!$cookie->hasKey($cksessionid)) return false;
    if (!$session->hasKey($cksessionid)) return false;
    $expected = hash('sha256', $session->get($cksessionid));
    $received = $cookie->get($cksessionid);
    return $expected === $received;
  }
}

class LoginInfo extends RawDataContainer {
  public function isLoggedIn(): bool {
    return $this->get('logged-in');
  }

  public function isAdmin(): bool {
    return $this->get('is-admin');
  }

  public function username(): string {
    return $this->getDefault('username', '');
  }

  public function password(): string {
    return $this->getDefault('password', '');
  }
}
?>
