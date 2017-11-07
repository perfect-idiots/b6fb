<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../view/index.php';

function main(): string {
  $urlQuery = new UrlQuery($_GET);

  $data = array(
    'url-query' => $urlQuery
  );

  return Page::instance($data)->render();
}
?>
