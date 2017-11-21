<?php
require_once __DIR__ . '/system-requirements.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/login.php';
require_once __DIR__ . '/logout.php';
require_once __DIR__ . '/sign-up.php';
require_once __DIR__ . '/db-game.php';
require_once __DIR__ . '/user-profile.php';
require_once __DIR__ . '/count-users.php';
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
      return AdminPage::instance($data);
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

function sendHtml(DataContainer $data): string {
  return switchPage($data->getData())->render();
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
  if (!file_exists($filename)) throw new NotFoundException();

  header('Content-Type: ' . $mime);
  header('Content-Length: ' . filesize($filename));
  header('Content-Disposition: inline');
  readfile($filename);
  exit;
}

function sendAction(DataContainer $param): string {
  $urlQuery = $param->get('url-query');
  $dbQuerySet = $param->get('db-query-set');
  $cookie = $param->get('cookie');
  $session = $param->get('session');
  $login = $param->get('login');
  $action = $urlQuery->getDefault('action', '');
  $dbQuerySet = DatabaseQuerySet::instance();

  switch ($action) {
    case 'edit-user':
      $username = $urlQuery->getDefault('username', '');
      $fullname = $urlQuery->getDefault('fullname', '');
      if (!$username || !$fullname) return ErrorPage::status(400)->render();
      $userProfileUpdater = UserProfileUpdater::instance([
        'cookie' => $cookie,
        'session' => $session,
        'login' => $login,
        'db-query-set' => $dbQuerySet,
        'username' => $urlQuery->get('username'),
      ]);
      $userProfileUpdater->update([
        'fullname' => $urlQuery->get('fullname'),
      ]);
      $urlQuery->without([
        'fullname',
        'previous-page',
      ])->assign([
        'type' => 'html',
        'subpage' => $urlQuery->get('previous-page'),
      ])->redirect();
      break;
    default:
      throw new NotFoundException();
  }
}

function main(): string {
  $constants = Constants::instance();
  $urlQuery = new UrlQuery($_GET);
  $postData = new HttpData($_POST);
  $page = $urlQuery->getDefault('page', 'index');

  $cookie = Cookie::instance([
    'expiry-extend' => $constants->get('month'),
  ]);

  if ($urlQuery->hasKey('theme')) {
    $cookie->set('theme', $urlQuery->get('theme'))->update();
    $urlQuery->except('theme')->redirect();
  }

  $themeColorSet = getThemeColorSet($cookie);

  if ($themeColorSet['invalid']) {
    $themeColorSet['new-cookie']->update();
    $urlQuery->except('theme')->redirect();
  }

  $session = Session::instance();
  $sizeSet = SizeSet::instance();
  $imageSet = ImageSet::instance($themeColorSet);
  $dbQuerySet = DatabaseQuerySet::instance();

  $accountParams = [
    'is-admin' => $page === 'admin',
    'session' => $session,
    'post-data' => $postData,
    'cookie' => $cookie,
    'db-query-set' => $dbQuerySet,
    'url-query' => $urlQuery,
  ];

  $signup = SignUp::instance($accountParams)->verify();
  $login = Login::instance($accountParams)->verify();
  $logout = Logout::instance($accountParams);

  $userCounter = UserCounter::instance([
    'cookie' => $cookie,
    'session' => $session,
    'db-query-set' => $dbQuerySet,
    'login' => $login,
  ]);

  $param = RawDataContainer::instance([
    'title' => 'b6fb',
    'url-query' => $urlQuery,
    'post-data' => $postData,
    'theme-name' => $themeColorSet['name'],
    'colors' => $themeColorSet['colors'],
    'images' => $imageSet->getData(),
    'size-set' => $sizeSet,
    'sizes' => $sizeSet->getData(),
    'page' => $page,
    'session' => $session,
    'cookie' => $cookie,
    'subpages' => createSubpageList($urlQuery, $cookie),
    'admin-page' => $urlQuery->getDefault('subpage', 'dashboard'),
    'admin-subpages' => createAdminSubpageList($urlQuery),
    'db-query-set' => $dbQuerySet,
    'game-inserter' => new GameInserter($dbQuerySet),
    'signup' => $signup,
    'login' => $login,
    'logout' => $logout,
    'user-counter' => $userCounter,
  ]);

  try {
    switch ($urlQuery->getDefault('type', 'html')) {
      case 'html':
        return sendHtml($param);
      case 'image':
        return sendImage($urlQuery);
      case 'action':
        return sendAction($param);
      default:
        throw new NotFoundException();
    }
  } catch (NotFoundException $err) {
    return ErrorPage::status(404)->render();
  } catch (SecurityException $err) {
    return ErrorPage::status(401)->render();
  }
}
?>
