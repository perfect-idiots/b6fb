<?php
require_once __DIR__ . '/system-requirements.php';
require_once __DIR__ . '/login.php';
require_once __DIR__ . '/logout.php';
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
    case 'profile':
    case 'explore':
    case 'favourite':
    case 'history':
      return MainPage::instance($data);
    case 'login':
      return LoginPage::instance($data);
    case 'logout':
      return LogoutPage::instance(array_merge($data, [
        'logout' => Logout::instance($data),
      ]));
    case 'sign-up':
      return SignUpPage::instance($data);
    case 'admin':
      return AdminPage::instance(array_merge($data, [
        'login' => Login::instance(array_merge($data, [
          'is-admin' => true,
        ]))->verify(),
      ]));
    default:
      throw new NotFoundException();
  }
}

function createSubpageList(UrlQuery $urlQuery, Cookie $cookie): array {
  $username = $cookie->getDefault('username', null);

  $customized = $username
    ? ['profile' => 'Tài khoản']
    : ['explore' => 'Khám phá']
  ;

  $namemap = array_merge($customized, [
    'favourite' => 'Yêu thích',
    'history' => 'Lịch sử',
  ]);

  $result = [[
    'page' => 'index',
    'title' => 'Trang chủ',
    'href' => '.',
  ]];

  foreach ($namemap as $page => $title) {
    $href = $urlQuery->set('page', $page)->getUrlQuery();

    array_push($result, [
      'page' => $page,
      'title' => $title,
      'href' => $href,
    ]);
  }

  return $result;
}

function createAdminSubpageList(UrlQuery $urlQuery) {
  $namemap = [
    'games' => 'Trò chơi',
    'users' => 'Người dùng',
    'advanced' => 'Nâng cao',
  ];

  $result = [[
    'subpage' => 'dashboard',
    'title' => 'Bảng điều khiển',
    'href' => UrlQuery::instance(['page' => 'admin'])->getUrlQuery(),
  ]];

  foreach ($namemap as $page => $title) {
    $href = $urlQuery->set('subpage', $page)->getUrlQuery();

    array_push($result, [
      'subpage' => $page,
      'title' => $title,
      'href' => $href,
    ]);
  }

  return $result;
}

function sendHtml(UrlQuery $urlQuery, HttpData $postData, Cookie $cookie): string {
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
  $dbQuerySet = DatabaseQuerySet::instance();

  $login = Login::instance([
    'post-data' => $postData,
    'cookie' => $cookie,
    'db-query-set' => $dbQuerySet,
    'url-query' => $urlQuery,
  ])->verify();

  $data = [
    'title' => 'b6fb',
    'url-query' => $urlQuery,
    'post-data' => $postData,
    'theme-name' => $themeColorSet['name'],
    'colors' => $themeColorSet['colors'],
    'images' => $imageSet->getData(),
    'size-set' => $sizeSet,
    'sizes' => $sizeSet->getData(),
    'page' => $urlQuery->getDefault('page', 'index'),
    'cookie' => $cookie,
    'subpages' => createSubpageList($urlQuery, $cookie),
    'admin-page' => $urlQuery->getDefault('subpage', 'dashboard'),
    'admin-subpages' => createAdminSubpageList($urlQuery),
    'db-query-set' => $dbQuerySet,
    'login' => $login,
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
  $postData = new HttpData($_POST);

  $cookie = Cookie::instance([
    'expiry-extend' => $constants->get('month'),
  ]);

  switch ($urlQuery->getDefault('type', 'html')) {
    case 'html':
      return sendHtml($urlQuery, $postData, $cookie);
    case 'image':
      return sendImage($urlQuery);
    default:
      return ErrorPage::status(404)->render();
  }
}
?>
