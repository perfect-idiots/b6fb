<?php
require_once __DIR__ . '/system-requirements.php';
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

function createSubpageList(UrlQuery $urlQuery, Cookie $cookie): array {
  $username = $cookie->getDefault('username', null);

  $customized = $username
    ? ['profile' => "$username's profile"]
    : ['login' => 'Login']
  ;

  $namemap = array_merge($customized, [
    'preferences' => 'Preferences',
    'favourite' => 'Starred',
    'history' => 'Recently Played',
  ]);

  $result = [];

  foreach ($namemap as $page => $title) {
    $targetUrlQuery = $urlQuery->set('page', $page);
    $href = $targetUrlQuery->getUrlQuery();

    $result[$page] = [
      'page' => $page,
      'title' => $title,
      'url-query' => $targetUrlQuery,
      'href' => $href,
    ];
  }

  return $result;
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
  $imageSet = ImageSet::instance($themeColorSet);

  $data = [
    'title' => 'b6fb',
    'url-query' => $urlQuery,
    'theme-name' => $themeColorSet['name'],
    'colors' => $themeColorSet['colors'],
    'images' => $imageSet->getData(),
    'size-set' => $sizeSet,
    'sizes' => $sizeSet->getData(),
    'page' => $urlQuery->getDefault('page', 'index'),
    'cookie' => $cookie,
    'subpages' => createSubpageList($urlQuery, $cookie),
  ];

  try {
    return switchPage($data)->render();
  } catch (NotFoundException $error) {
    return ErrorPage::status(404)->render();
  }
}

function sendImage(UrlQuery $urlQuery): string {
  $requiredkeys = ['name', 'mime'];
  foreach ($requiredkeys as $key) {
    if (!$urlQuery->hasKey($key)) return ErrorPage::status(400)->render();
  }

  $name = $urlQuery->get('name');
  $mime = $urlQuery->get('mime');
  if (preg_match('/^\/|(^|\/)\.\.($|\/)/', $name)) return ErrorPage::status(403)->render();

  $filename = __DIR__ . '/../resources/images/' . $name;
  if (!file_exists($filename)) return ErrorPage::status(404)->render();

  header('Content-Type: ' . $mime);
  header('Content-Length: ' . filesize($filename));
  header('Content-Disposition: inline');
  readfile($filename);
  exit;
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
    case 'image':
      return sendImage($urlQuery);
    default:
      return ErrorPage::status(404)->render();
  }
}
?>
