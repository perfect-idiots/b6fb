<?php
require_once __DIR__ . '/../lib/utils.php';

class UrlQuery extends RawDataContainer {
  private $prefix, $separator;

  static public function from(array $data): self {
    return new static($data);
  }

  public function getUrlQuery(): string {
    return '?' . http_build_query($this->getData());
  }
}

$GLOBALS['URL_QUERY'] = new UrlQuery($_GET);
?>
