<?php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../lib/utils.php';

class UserProfileUpdater extends LoginDoubleChecker {
  static protected function requiredFieldSchema(): array {
    return array_merge(parent::requiredFieldSchema(), [
      'username' => 'string',
    ]);
  }

  public function checkPermission(): bool {
    $login = $this->get('login');
    return $login->username() === $this->get('username') || $this->isAdmin();
  }

  public function update(array $param): DatabaseQuerySingleResult {
    $this->verify();
    $dbQuerySet = $this->get('db-query-set');
    $query = $dbQuerySet->get('update-user-profile');
    $username = $param['username'];

    $profile = UserInfo::instance([
      'username' => $username,
      'db-query-set' => $dbQuerySet,
    ])->fetch();

    [
      'fullname' => $fullname,
    ] = array_merge($profile, $param);

    return $query->executeOnce([$fullname, $username]);
  }
}

class UserInfo extends RawDataContainer {
  private $loaded = null;

  static protected function requiredFieldSchema(): array {
    return [
      'username' => 'string',
      'db-query-set' => 'DatabaseQuerySet',
    ];
  }

  public function fetch(): array {
    if ($this->loaded) return $this->loaded;
    $username = $this->username();

    $dbResult = $this
      ->get('db-query-set')
      ->get('user-info')
      ->executeOnce([$username], 1)
      ->fetch()
    ;

    if (!sizeof($dbResult)) throw new Exception("User '$username' does not exist");
    [$fullname] = $dbResult;

    $this->loaded = [
      'username' => $username,
      'fullname' => $fullname,
    ];

    return $this->loaded;
  }

  public function username(): string {
    return $this->get('username');
  }

  public function fullname(): string {
    return $this->fetch()['fullname'];
  }
}
?>
