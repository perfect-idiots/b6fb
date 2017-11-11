<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../view/index.php';
require_once __DIR__ . '/../lib/constants.php';

function getThemeColorSet(Cookie $cookie): array {
  $themeName = $cookie->getDefault('theme', 'light');
  $themeColorSet = null;

  switch ($themeName) {
    case 'light':
      $themeColorSet = LightThemeColors::create();
      break;
    case 'dark':
      $themeColorSet = DarkThemeColors::create();
      break;
    default:
      return [
        'invalid' => true,
        'new-cookie' => $cookie->set('theme', 'light'),
      ];
  }

  return [
    'invalid' => false,
    'name' => $themeName,
    'colors' => $themeColorSet->getData(),
  ];
}

function switchPage(array $data): Page {
  switch ($data['page']) {
    case 'index':
      return MainPage::instance($data);
    case 'admin':
      return AdminPage::instance($data);
    default:
      throw new NotFoundException();
  }
}

function sendHtml(UrlQuery $urlQuery, Cookie $cookie): string {
  if ($urlQuery->hasKey('theme')) {
    $cookie->set('theme', $urlQuery->get('theme'))->update();
    $urlQuery->except('theme')->redirect();
  }

  $themeColorSet = getThemeColorSet($cookie);

  if ($themeColorSet['invalid']) {
    $themeColorSet['new-cookie']->update();
    $urlQuery->except('theme')->redirect();
  }

  $sizeSet = SizeSet::instance();

  $data = [
    'title' => 'b6fb',
    'url-query' => $urlQuery,
    'theme-name' => $themeColorSet['name'],
    'colors' => $themeColorSet['colors'],
    'size-set' => $sizeSet,
    'sizes' => $sizeSet->getData(),
    'page' => $urlQuery->getDefault('page', 'index'),
    'cookie' => $cookie,
  ];

  try {
    return switchPage($data)->render();
  } catch (NotFoundException $error) {
    return ErrorPage::status(404)->render();
  }
}

function main(): string {
  $constants = Constants::instance();
  $urlQuery = new UrlQuery($_GET);

  $cookie = Cookie::instance([
    'expiry-extend' => $constants->get('month'),
  ]);

  switch ($urlQuery->getDefault('type', 'html')) {
    case 'html':
      return sendHtml($urlQuery, $cookie);
    default:
      return ErrorPage::status(404)->render();
  }
}
?>
