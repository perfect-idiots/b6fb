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

function sendHtml(UrlQuery $urlQuery): string {
  $themeColorSet = getThemeColorSet($urlQuery);

  $data = array(
    'title' => 'b6fb',
    'url-query' => $urlQuery,
    'theme-name' => $themeColorSet['name'],
    'colors' => $themeColorSet['colors'],
  );

  try {
    return MainPage::instance($data)->render();
  } catch (NotFoundException $error) {
    return ErrorPage::status(404)->render();
  }
}

function main(): string {
  $urlQuery = new UrlQuery($_GET);

  switch($urlQuery->getDefault('type', 'html')) {
    case 'html':
      return sendHtml($urlQuery);
    default:
      return ErrorPage::status(404)->render();
  }
}
?>
