<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../view/index.php';

function main(): string {
  $urlQuery = new UrlQuery($_GET);

  $themeName = $urlQuery->getDefault('theme', 'light');
  $themeColorSet = null;

  switch($themeName) {
    case 'light':
      $themeColorSet = new LightThemeColors();
      break;
    case 'dark':
      $themeColorSet = new DarkThemeColors();
      break;
    default:
      $urlQuery->set('theme', 'light')->redirect();
  }

  $data = array(
    'url-query' => $urlQuery,
    'colors' => $themeColorSet->getData(),
  );

  return Page::instance($data)->render();
}
?>
