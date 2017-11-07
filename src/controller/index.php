<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../view/index.php';

function main(): string {
  $urlQuery = new UrlQuery($_GET);
  return Page::instance(array())->render();
}
?>
