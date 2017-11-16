<?php
require_once __DIR__ . '/../lib/utils.php';

class DatabaseInfo extends LazyLoadedDataContainer {
  protected function load(): array {
    $dirname = realpath(__DIR__ . '/database');
    $info = "$dirname/database.php";

    if (!file_exists($info)) {
      die("
        <h1>Missing file</h1>
        <p>
          File <code>$dirname/<b>database.php</b></code> is missing.
        </p>
        <p>
          Please read
          <code>$dirname/<b>README.md</b></code>
          for further instructions.
        </p>
      ");
    }

    $result = require $info;
    if (gettype($result) !== 'array') {
      die("
        <h1>Invalid return type</h1>
        <p>
          Code at <code>$dirname/<b>database.php</b></code>
          must return an array.
        </p>
      ");
    }

    $required = ['domain', 'username', 'password', 'dbname'];
    foreach ($required as $key) {
      if (!array_key_exists($key, $result)) {
        die("
          <h1>Missing field</h1>
          <p>
            Field <code><b>$key</b></code> is missing
            from file <code>$info</code>.
          </p>
        ");
      }

      if (gettype($result[$key]) !== 'string') {
        die("
          <h1>Missing field</h1>
          <p>
            Field <code><b>$key</b></code>
            from file <code>$info</code>
            must be a string.
          </p>
        ");
      }
    }

    return $result;
  }
}

class DatabaseConnection extends DatabaseInfo {
  private $loaded = false;

  protected function load(): array {
    $info = parent::load();
    $link = new mysqli($info['domain'], $info['username'], $info['password'], $info['dbname']);

    if (mysqli_connect_errno()) {
      $error = mysqli_connect_error();

      die("
        <h1>Connection Error</h1>
        <p>
          Failed to connect to
          <code>{$info['username']}@{$info['domain']}</code>
          using account {$info['username']}.
        </p>
        <p><pre><code>$error</code></pre></p>
      ");
    }

    $this->loaded = true;
    return array_merge($info, ['info' => $info, 'link' => $link]);
  }

  public function __destruct() {
    if ($this->loaded) mysqli_close($this->get('link'));
  }
}

class DatabaseQuerySet extends DatabaseConnection {
  private function createQueries(mysqli $link): array {
    $login = function ($table) {
      return "SELECT count(*) as ok FROM $table WHERE username = ? and hashed_password = ?";
    };

    $queries = [
      'verify-admin-login' => $login('admin_accounts'),
      'verify-user-login' => $login('user_accounts'),
    ];

    return array_map(
      function ($query) use($link) {
        return $link->prepare($query);
      },
      $queries
    );
  }
}
?>
