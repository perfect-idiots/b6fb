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
      return [
        'template' => "SELECT count(*) as ok FROM $table WHERE username = ? and password_hash = ?",
        'format' => 'ss',
      ];
    };

    $queries = [
      'verify-admin-login' => 'ss',
      'verify-user-login' => 'ss',
      'create-account' => 'sss'
    ];

    $queries = [];
    foreach ($queryFormats as $name => $format) {
      $queries[$name] = [
        'template' => file_get_contents(__DIR__ . "/db-queries/$name.sql"),
        'format' => $format,
      ];
    }

    return array_map(
      function ($desc) use($link) {
        return new DatabaseQueryStatement(array_merge($desc, ['link' => $link]));
      },
      $queries
    );
  }
}

class DatabaseQueryStatement extends RawDataContainer {
  private $statement = null;

  public function __construct(array $desc) {
    parent::__construct($desc);
    $this->init();
  }

  public function __destruct() {
    $this->clear();
  }

  private function init(): void {
    $this->clear();
    $this->statement = $link->prepare($desc['template']);
  }

  private function clear(): void {
    if ($this->statement) $this->statement->close();
    $this->statement = null;
  }

  static protected function requiredFieldSchema(): array {
    return [
      'template' => 'string',
      'format' => 'string',
      'link' => 'mysqli',
    ];
  }

  public function executeOnce(array $param): array {
    $statement = $this->statement;

    $bindSuccess = call_user_func_array(
      [$statement, 'bind_param'],
      array_merge([$this->get('format')], $param)
    );

    if (!$bindSuccess) throw new Exception('Cannot bind param');

    return [
      'success' => $statement->execute(),
      'statement' => $statement,
    ];
  }

  public function executeMultipleTimes(array $param): array {
    $statement = $this->statement;
    $success = [];

    foreach ($param as $index => $subparam) {
      $bindSuccess = call_user_func_array(
        [$statement, 'bind_param'],
        array_merge([$this->get('format')], $param)
      );

      if (!$bindSuccess) throw new Exception("Cannot bind param[$index]");
      $success[$index] = $statement->execute();
    }

    return [
      'success' => $success,
      'statement' => $statement,
    ];
  }
}
?>
