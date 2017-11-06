<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../view/index.php';

function main(): string {
  return Page::instance(array())->render();
}
?>
