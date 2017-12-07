<?php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/../lib/utils.php';

class UserProfile extends LoginDoubleChecker {
  public function checkPermission(): bool {
    $login = $this->get('login');
    return $login->username() === $this->username() || $login->isAdmin();
  }

  public function info(): array {
    $this->verify();
    $username = $this->get('login')->username();

    [[$fullname]] = $this
      ->get('db-query-set')
      ->get('user-info')
      ->executeOnce([$username], 2)
      ->fetch()
    ;

    return [
      $fullname,
      $username,
      'fullname' => $fullname,
      'username' => $username,
    ];
  }

  public function update(array $param): DatabaseQuerySingleResult {
    $this->verify();
    $dbQuerySet = $this->get('db-query-set');
    $username = $this->username();
    $query = $dbQuerySet->get('update-user-profile');

    $profile = UserInfo::instance([
      'username' => $username,
      'db-query-set' => $dbQuerySet,
    ])->fetch();

    [
      'fullname' => $fullname,
    ] = array_merge($profile, $param);

    return $query->executeOnce([$fullname, $username]);
  }

  public function updatePassword(string $password): void {
    $this->verify();
    $username = $this->username();
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $this
      ->get('db-query-set')
      ->get('update-user-password')
      ->executeOnce([$hash, $username])
    ;
  }

  public function getHistory(): array {
    $this->verify();
    $username = $this->username();

    $list = $this
      ->get('db-query-set')
      ->get('get-history-by-user')
      ->executeOnce([$username], 2 + 2 + 1)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'game-id' => $row[0],
          'date' => $row[1],
          'game-name' => $row[2],
          'player-name' => $row[3],
          'player-id' => $row[4],
        ]);
      },
      $list
    );
  }

  public function addHistory(string $game): DatabaseQuerySingleResult {
    $this->verify();
    $username = $this->username();

    return $this
      ->get('db-query-set')
      ->get('add-user-playing-history')
      ->executeOnce([$username, $game])
    ;
  }

  public function clearHistory(): void {
    $this->verify();
    $username = $this->get('login')->username();

    $this
      ->get('db-query-set')
      ->get('clear-history-by-user')
      ->executeOnce([$username])
    ;
  }

  public function checkFavourite(string $id): bool {
    $this->verify();
    $username = $this->username();

    [[$count]] = $this
      ->get('db-query-set')
      ->get('user-favourite-existence')
      ->executeOnce([$username, $id], 1)
      ->fetch()
    ;

    return (bool) $count;
  }

  public function addFavourite(string $id): void {
    $this->verify();
    $username = $this->username();

    $this
      ->get('db-query-set')
      ->get('add-favourite')
      ->executeOnce([$username, $id])
    ;
  }

  public function deleteFavourite(string $id): void {
    $this->verify();
    $username = $this->username();

    $this
      ->get('db-query-set')
      ->get('delete-favourite')
      ->executeOnce([$username, $id])
    ;
  }


  public function listFavourite(): array {
    $username = $this->username();

    $list = $this
      ->get('db-query-set')
      ->get('list-favourite-games')
      ->executeOnce([$username], 3 + 2)
      ->fetch()
    ;

    return array_map(
      function (array $row) {
        return array_merge($row, [
          'id' => $row[0],
          'name' => $row[1],
          'genre' => splitAndCombine($row[2], $row[3]),
          'description' => $row[4],
        ]);
      },
      $list
    );
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
      ->executeOnce([$username], 2)
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
