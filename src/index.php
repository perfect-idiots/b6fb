<?php
require_once __DIR__ . '/lib/components/app.php';
require_once __DIR__ . '/lib/render.php';

$renderer = new Renderer();

echo '<!DOCTYPE html>';

echo $renderer->render(
  new App()
);
?>
