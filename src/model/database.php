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

    $link->query('set character_set_results=utf8');
    mb_language('uni');
    mb_internal_encoding('UTF-8');
    $link->query('set names "utf8"');
    $this->loaded = true;
    return array_merge($info, ['info' => $info, 'link' => $link]);
  }

  public function __destruct() {
    if ($this->loaded) mysqli_close($this->get('link'));
  }
}

class DatabaseQuerySet extends DatabaseConnection {
  protected function load(): array {
    $data = parent::load();
    $link = $data['link'];
    $queries = $this->createQueries($link);

    return array_merge($data, $queries, [
      'source' => $data,
      'link' => $link,
      'queries' => $queries,
    ]);
  }

  public function queries(): array {
    return $this->get('queries');
  }

  public function query(string $name): DatabaseQueryStatement {
    return $this->queries()[$name];
  }

  private function createQueries(mysqli $link): array {
    $queryFormats = [
      'user-password' => 's',
      'admin-password' => 's',
      'create-account' => 'sss',
      'user-account-existence' => 's',
      'game-existence' => 's',
      'list-games' => '',
      'list-users' => '',
      'list-genres' => '',
      'add-game' => 'ssss',
      'user-info' => 's',
      'update-user-fullname' => 'ss',
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
    $link = $this->get('link');
    $template = $this->get('template');
    $this->clear();
    $this->statement = $link->prepare($template);
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

  public function executeOnce(array $param, int $columns = 0): DatabaseQuerySingleResult {
    $statement = $this->statement;

    if (!sizeof($param)) {
        return DatabaseQuerySingleResult::instance([
        'success' => $statement->execute(),
        'statement' => $statement,
        'columns' => $columns,
      ]);
    }

    $refs = $this->arrOfRefs($param);

    $bindSuccess = call_user_func_array(
      [$statement, 'bind_param'],
      array_merge([$this->get('format')], $refs)
    );

    if (!$bindSuccess) throw new Exception('Cannot bind param');

    return DatabaseQuerySingleResult::instance([
      'success' => $statement->execute(),
      'statement' => $statement,
      'columns' => $columns,
    ]);
  }

  public function executeMultipleTimes(array $param): array {
    $refs = $this->arrOfRefs($param);
    $statement = $this->statement;
    $success = [];

    foreach ($param as $index => $subparam) {
      $bindSuccess = call_user_func_array(
        [$statement, 'bind_param'],
        array_merge([$this->get('format')], $refs)
      );

      if (!$bindSuccess) throw new Exception("Cannot bind param[$index]");
      $success[$index] = $statement->execute();
    }

    return [
      'success' => $success,
      'statement' => $statement,
    ];
  }

  private function arrOfRefs(array &$array): array {
    $refs = [];
    foreach ($array as $key => &$value) {
      $refs[$key] = &$value;
    }
    return $refs;
  }
}

abstract class DatabaseQueryResult extends RawDataContainer {}

class DatabaseQuerySingleResult extends DatabaseQueryResult {
  private $result = null;

  static protected function requiredFieldSchema(): array {
    return [
      'success' => 'boolean',
      'statement' => 'mysqli_stmt',
      'columns' => 'integer',
    ];
  }

  public function fetch(): array {
    if ($this->result) return $this->result;
    $this->result = $this->fetchMain();
    return $this->result;
  }

  public function rows(): int {
    return sizeof($this->fetch());
  }

  public function success(): bool {
    return $this->get('success');
  }

  public function statement(): mysqli_stmt {
    return $this->get('statement');
  }

  public function columns(): int {
    return $this->get('columns');
  }

  private function fetchMain(): array {
    [
      'success' => $success,
      'statement' => $statement,
      'columns' => $columns,
    ] = $this->getData();

    if (!$success) {
      throw new Exception('Atempt to fetch result of an unsuccessful execution');
    }

    $result = []; // array of arrays
    $buffer = []; // array to be referered to
    $refs = []; // array of references

    foreach (range(0, $columns - 1) as $index) {
      $buffer[$index] = null;
      $refs[$index] = &$buffer[$index];
    }

    call_user_func_array([$statement, 'bind_result'], $refs);

    while ($statement->fetch()) {
      $row = [];
      foreach ($buffer as $key => $value) {
        $row[$key] = $value;
      }
      array_push($result, $row);
    }

    return $result;
  }
}
?>
