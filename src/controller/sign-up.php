<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/login.php';

class SignUp extends RawDataContainer {
  public function verify(): SignUpInfo {
    $session = $this->get('session');
    $postData = $this->get('post-data');
    $cookie = $this->get('cookie');
    $urlQuery = $this->get('url-query');
    $dbQuerySet = $this->get('db-query-set');
    $fullname = $postData->getDefault('fullname', '');
    $username = $postData->getDefault('username', '');
    $password = $postData->getDefault('password', '');
    $rePassword = $postData->getDefault('re-password', '');

    if ($postData->getDefault('signed-up', 'off') === 'on') {
      $signup = static::checkSignUp([
        'fullname' => $fullname,
        'username' => $username,
        'password' => $password,
        're-password' => $rePassword,
        'db-query-set' => $dbQuerySet,
      ]);

      if ($signup->succeed()) {
        $session->without([
          'fullname',
          'username',
          'password',
          're-password',
        ])->update();

        $session->without([
          'sign-up-succeed',
          'sign-up-error-field',
          'sign-up-error-reason',
        ])->update();

        $cookie->assign([
          'logged-in' => 'on',
          'username' => $username,
          'password' => $password,
        ])->update();

        $login = LoginInfo::instance([
          'username' => $username,
          'password' => $password,
        ]);

        $query = $dbQuerySet->get('create-account');
        $dbResponse = $query->executeOnce([
          $fullname,
          $username,
          password_hash($password, PASSWORD_BCRYPT),
        ]);

        $urlQuery->set('page', $urlQuery->get('previous-page'))->redirect();
      } else {
        $error = $signup->error();

        $session->assign([
          'sign-up-succeed' => 'off',
          'sign-up-error-field' => $error['field'],
          'sign-up-error-reason' => $error['reason'],
        ])->update();

        return $signup;
      }
    }

    return SignUpInfo::instance();
  }

  static public function checkSignUp(array $param): SignUpInfo {
    [
      'fullname' => $fullname,
      'username' => $username,
      'password' => $password,
      're-password' => $rePassword,
      'db-query-set' => $dbQuerySet,
    ] = $param;

    $userAccountExistence = $dbQuerySet
      ->get('user-account-existence')
      ->executeOnce([$username], 1)
      ->rows()
    ;

    if (!$fullname) return SignUpInfo::mkerror('fullname', 'empty');
    if (!$username) return SignUpInfo::mkerror('username', 'empty');
    if ($userAccountExistence) return SignUpInfo::mkerror('username', 'taken');
    if (!$password) return SignUpInfo::mkerror('password', 'empty');
    if (strlen($password) < 6) return SignUpInfo::mkerror('password', 'insufficient-length');
    if ($password !== $rePassword) return SignUpInfo::mkerror('re-password', 'mismatch');

    return SignUpInfo::instance(array_merge($param, [
      'succeed' => true,
    ]));
  }
}

class SignUpInfo extends RawDataContainer {
  public function succeed(): bool {
    return $this->getDefault('succeed', false);
  }

  public function error(): ?array {
    return $this->getDefault('error', null);
  }

  public function login(): ?LoginInfo {
    return $this->getDefault('login', null);
  }

  static public function mkerror(string $field, string $reason): self {
    return self::instance([
      'succeed' => false,
      'error' => [
        'field' => $field,
        'reason' => $reason,
      ],
    ]);
  }
}
?>
