<?php
require_once __DIR__ . '/system-requirements.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/login.php';
require_once __DIR__ . '/logout.php';
require_once __DIR__ . '/sign-up.php';
require_once __DIR__ . '/db-game.php';
require_once __DIR__ . '/db-genre.php';
require_once __DIR__ . '/db-user.php';
require_once __DIR__ . '/db-admin.php';
require_once __DIR__ . '/user-profile.php';
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
    case 'genre':
    case 'play':
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
    ? [
      'profile' => 'Tài khoản',
      'favourite' => 'Yêu thích',
    ]
    : [
      'explore' => 'Khám phá',
      'sign-up' => 'Tham gia',
    ]
  ;

  $namemap = array_merge($customized, [
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

function validateFileName(string $name): void {
  if (preg_match('/^\/|(^|\/)\.\.($|\/)/', $name)) {
    ErrorPage::status(403)->render();
    throw new NotFoundException();
  }
}

function getFilePath(UrlQuery $urlQuery): string {
  $name = $urlQuery->get('name');
  validateFileName($name);

  switch ($urlQuery->get('purpose')) {
    case 'ui':
      return __DIR__ . "/../resources/images/$name";
    case 'game-img':
      return __DIR__ . "/../storage/game-imgs/$name";
    case 'game-swf':
      return __DIR__ . "/../storage/game-swfs/$name";
    default:
      throw new NotFoundException();
  }
}

function sendFile(UrlQuery $urlQuery): string {
  $requiredkeys = ['name', 'mime', 'purpose'];
  foreach ($requiredkeys as $key) {
    if (!$urlQuery->hasKey($key)) return ErrorPage::status(400)->render();
  }

  $mime = $urlQuery->get('mime');
  $filename = getFilePath($urlQuery);
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
    case 'check-admin-auth':
      $param->get('login-double-checker')->verify();
      return '
        <strong>Authenticated</strong>
      ';

    case 'edit-user':
      $username = $urlQuery->getDefault('username', '');
      $fullname = $urlQuery->getDefault('fullname', '');
      if (!$username || !$fullname) return ErrorPage::status(400)->render();
      $param->get('user-manager')->update($username, $fullname);

      $urlQuery->without([
        'action',
        'fullname',
        'previous-page',
      ])->assign([
        'type' => 'html',
        'subpage' => $urlQuery->get('previous-page'),
      ])->redirect();
      break;

    case 'delete-user':
      $username = $urlQuery->getDefault('username', '');
      $param->get('user-manager')->delete($username);

      $urlQuery->without([
        'action',
        'username',
      ])->assign([
        'type' => 'html',
        'page' => 'admin',
        'subpage' => 'users',
      ])->redirect();
      break;

    case 'reset-database':
      $postData = $param->get('post-data');
      $urlQuery = $param->get('url-query');
      $password = $postData->getDefault('password', '');

      if ($postData->getDefault('confirmed', 'off') === 'on') {
        $loginDoubleChecker = $param->get('login-double-checker');
        $loginDoubleChecker->set(
          'login',
          $loginDoubleChecker
            ->get('login')
            ->set('password', $password)
        )->verify();

        $check = function (string $key) use($urlQuery) {
          return $urlQuery->getDefault($key, 'off') === 'on';
        };

        if ($check('game')) {
          $param->get('genre-manager')->reset();
          $param->get('game-manager')->reset();
        }

        if ($check('user')) {
          $param->get('user-manager')->reset();
        }

        if ($check('admin')) {
          $param->get('admin-manager')->reset();
        }
      }

      $urlQuery->except('action')->assign([
        'type' => 'html',
        'page' => 'admin',
        'subpage' => 'advanced',
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
  $files = UploadedFileSet::instance();
  $predefinedGames = PredefinedGames::create();
  $predefinedGenres = PredefinedGenres::create();
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

  $securityCommonParam = ([
    'cookie' => $cookie,
    'session' => $session,
    'db-query-set' => $dbQuerySet,
    'login' => $login,
  ]);

  $loginDoubleChecker = new LoginDoubleChecker($securityCommonParam);
  $gameManager = new GameManager($securityCommonParam);
  $genreManager = new GenreManager($securityCommonParam);
  $userManager = new UserManager($securityCommonParam);
  $adminManager = new AdminManager($securityCommonParam);

  $param = RawDataContainer::instance([
    'title' => 'b6fb',
    'url-query' => $urlQuery,
    'post-data' => $postData,
    'files' => $files,
    'predefined-games' => $predefinedGames,
    'predefined-genres' => $predefinedGenres,
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
    'login-double-checker' => $loginDoubleChecker,
    'game-manager' => $gameManager,
    'genre-manager' => $genreManager,
    'user-manager' => $userManager,
    'admin-manager' => $adminManager,
    'signup' => $signup,
    'login' => $login,
    'logout' => $logout,
  ]);

  try {
    switch ($urlQuery->getDefault('type', 'html')) {
      case 'html':
        return sendHtml($param);
      case 'file':
        return sendFile($urlQuery);
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
