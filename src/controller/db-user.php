<?php
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/user-profile.php';

class UserManager extends LoginDoubleChecker {
  public function list(): array {
    $this->verify();

    return $this
      ->get('db-query-set')
      ->get('list-users')
      ->executeOnce([], 2)
      ->fetch()
    ;
  }

  public function clear(): void {
    $this->verify();
    $dbQuerySet = $this->get('db-query-set');

    $exec = function (string $query) use($dbQuerySet) {
      $dbQuerySet->get($query)->executeOnce();
    };

    $exec('clear-users');
    $exec('clear-history');
    $exec('clear-favourites');
  }

  public function reset(): void {
    $this->verify();
    $this->clear();
  }

  public function update(string $username, string $fullname): void {
    $this->verify();

    $this
      ->get('db-query-set')
      ->get('update-user-profile')
      ->executeOnce([$fullname, $username])
    ;
  }

  public function delete(string $username): void {
    $this->verify();
    $dbQuerySet = $this->get('db-query-set');

    $exec = function (string $query) use($dbQuerySet, $username) {
      $dbQuerySet->get($query)->executeOnce([$username]);
    };

    $exec('delete-user');
    $exec('clear-history-by-user');
    $exec('clear-favourites-by-user');
  }

  public function getUserInfo(string $username): ?array {
    $dbResult = $this
      ->get('db-query-set')
      ->get('user-info')
      ->executeOnce([$username], 2)
      ->fetch()
    ;

    return sizeof($dbResult)
      ? $dbResult[0]
      : null
    ;
  }

  public function getUserExistence(string $username): bool {
    return (bool) $this->getUserInfo($username);
  }

  public function getUserFullname(string $username): ?string {
    return $this->getUserSpecificInfo($username, 0);
  }

  public function getUserUsername(string $username): ?string {
    return $this->getUserSpecificInfo($username, 1);
  }

  private function getUserSpecificInfo(string $username, int $index): ?string {
    $info = $this->getUserInfo($username);
    return $info ? $info[$index] : null;
  }

  public function count(): int {
    $this->verify();

    [[$count]] = $this
      ->get('db-query-set')
      ->get('count-users')
      ->executeOnce([], 1)
      ->fetch()
    ;

    return $count;
  }

  public function getUserProfile(string $username): UserProfile {
    return new UserProfile(
      $this->set('username', $username)->getData()
    );
  }
}
?>
