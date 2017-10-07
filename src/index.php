<?php
require_once __DIR__ . '/lib/components/app.php';
require_once __DIR__ . '/lib/render.php';

$renderer = new Renderer();

echo "<!DOCTYPE html>\n";

echo $renderer->render(
  new App()
);
?>
