<?php
require_once __DIR__ . '/../lib/utils.php';

class UrlQuery extends DataContainer {
  private $prefix, $separator;

  static public function from(array $data): self {
    return new self($data);
  }

  public function getUrlQuery(): string {
    return '?' . http_build_query($this->getData());
  }
}
?>
