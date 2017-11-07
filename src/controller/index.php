<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../view/index.php';

function getThemeColorSet(UrlQuery $urlQuery): array {
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

  return array(
    'name' => $themeName,
    'colors' => $themeColorSet->getData(),
  );
}

function main(): string {
  $urlQuery = new UrlQuery($_GET);
  $themeColorSet = getThemeColorSet($urlQuery);

  $data = array(
    'title' => 'b6fb',
    'url-query' => $urlQuery,
    'theme-name' => $themeColorSet['name'],
    'colors' => $themeColorSet['colors'],
  );

  return Page::instance($data)->render();
}
?>
