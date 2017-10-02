<?php
class Database {
  private $link;

  public function __construct(string $host, string $username, string $password, string $dbname) {
    $link = mysqli_connect($host, $username, $password, $dbname);

    if ($link) {
      $this->link = $link;
    } else {
      throw new Exception('Cannot connect database.');
    }
  }

  public function __destruct() {
    mysqli_close($this->link);
  }

  public function getError(): string {
    return mysqli_error($this->link);
  }

  public function query(string $sql): mysqli_result {
    $result = mysqli_query($this->link, $sql);

    if ($result) {
      return $result;
    } else {
      throw new Exception($this->getError());
    }
  }

  public function queryFetchAll(string $sql) {
    return mysqli_fetch_all($this->query($sql));
  }

  public function queryTable(string $sql): array {
    return QueryTable($this, $sql);
  }
}

class QueryTable {
  public $representative, $fields, $payload;

  public function __construct(Database $db, string $sql) {
    $representative = $this->representative = $db->query($sql);
    $this->fields = mysqli_fetch_fields($representative);
    $this->payload = mysqli_fetch_all($representative);
  }

  public function toArray(): array {
    return array(
      'representative' => $this->representative,
      'fields' => $this->fields,
      'payload' => $this->payload
    );
  }
}
?>
