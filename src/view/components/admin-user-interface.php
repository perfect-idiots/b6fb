<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/header-section.php';
require_once __DIR__ . '/sidebar-navigator.php';
require_once __DIR__ . '/hidden-input.php';
require_once __DIR__ . '/../../lib/utils.php';

class AdminUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $login = $this->get('login');
    $username = $login->username();
    $isLoggedIn = $login->isLoggedIn();
    $cssFileName = $isLoggedIn ? 'admin' : 'login';
    $images = $this->get('images');
    $dbQuerySet = $this->get('db-query-set');
    $listGames = $dbQuerySet->get('list-games')->executeOnce([], 4)->fetch();

    return HtmlElement::create('html', [
      'lang' => 'en',
      'dataset' => [
        'username' => $username,
      ],
      'classes' => [
        'admin',
        $isLoggedIn ? 'logged-in' : 'anonymous',
      ],
      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', 'Administration'),
        CssView::fromFile(__DIR__ . "/../../resources/$cssFileName.css"),
      ]),
      $isLoggedIn
        ? HtmlElement::create('body', [
          HtmlElement::emmetBottom('header#main-header', [
            HtmlElement::emmetTop('a#title-header', [
              'href' => $urlQuery->set('page', 'admin')->getUrlQuery(),
              'Administrator',
            ]),
            HtmlElement::emmetBottom('#username-admin', [
              $login->username(),
            ]),
            HtmlElement::emmetBottom('#logo-admin>button#profile-button', [
              HtmlElement::emmetTop('img#popup-profile-image', [
                'src' => $images['default-avatar-image'],
              ]),
            ]),
            HtmlElement::emmetTop('#profile-setting', [
              'hidden' => true,
              HtmlElement::emmetBottom('#profile-admin', [
                'Hồ sơ'
              ]),
              HtmlElement::emmetBottom('#setting-admin', [
                'Cài đặt',
              ]),
              HtmlElement::emmetBottom('#logout-admin>a', [
                'href' => $urlQuery->set('subpage', 'logout')->getUrlQuery(),
                'Đăng xuất',
              ]),
            ]),
          ]),
          HtmlElement::create('main', [
            new AdminNavigatorSection($this->getData()),
            new AdminMainSection($this->getData()),
          ]),
          JavascriptEmbed::file(__DIR__ . '/../../resources/scripts/script.js'),
        ])
        : HtmlElement::emmetBottom('body#login-page>#page.aligner', [
          HtmlElement::emmetTop('.top-aligned.aligned-item', []),
          HtmlElement::emmetTop('.middle-aligned.aligned-item', [
            HtmlElement::create('header', Logo::instance($this->getData())),
            HtmlElement::emmetBottom('section#main-section', [
              HtmlElement::emmetTop('h1#login-title', [
                HtmlElement::emmetTop('span.login-title', 'Đăng nhập'),
                HtmlElement::emmetTop('span.login-subtitle', '(Admin)'),
              ]),
              HtmlElement::create('main', [
                new LoginForm([
                  'action' => $urlQuery->getUrlQuery(),
                  'hidden-values' => [
                    'logged-in' => 'on',
                  ],
                ]),
              ]),
            ]),
          ]),
          HtmlElement::emmetTop('.bottom-aligned.aligned-item', []),
        ]),
    ]);
  }
}

class AdminNavigatorSection extends RawDataContainer implements Component {
  public function render(): Component {
    $currentpage = $this->get('admin-page');
    $subpagetmpls = $this->get('admin-subpages');

    return new SidebarNavigator(
      $subpagetmpls,
      function ($tmpl) {
        return [
          'href' => $tmpl['href'],
          $tmpl['title']
        ];
      },
      function ($tmpl) use($currentpage) {
        return $tmpl['subpage'] === $currentpage;
      }
    );
  }
}

class AdminMainSection extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();
    $page = $data['admin-page'];

    switch ($page) {
      case 'logout':
        $this->get('logout')->set('prefix', 'admin-')->act();
        $this->get('url-query')->except('subpage')->redirect();
        return new TextNode('Logged Out');
      case 'dashboard':
        return new AdminDashboard($data);
      case 'games':
        return new AdminGames($data);
      case 'users':
        return new AdminUsers($data);
      case 'advanced':
        return new AdminAdvanced($data);
      case 'edit-user':
        return new AdminEditUser($data);
      case 'add-game' :
        return new AdminAddGame($data);
      default:
        throw new NotFoundException();
    }
  }
}

class AdminDashboard extends RawDataContainer implements Component {
  public function render(): Component {
    $images = $this->get('images');
    $dbRowCounter = $this->get('db-row-counter');

    return HtmlElement::emmetBottom('#dashboard', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h1', 'Dashboard'),
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        DashboardPanel::create($this, 'game', 'gamepad-image', 'Trò chơi', 12),
        DashboardPanel::create($this, 'user', 'multi-users-image', 'Người dùng', $dbRowCounter->countUsers()),
      ]),
    ]);
  }
}

class DashboardPanel extends RawDataContainer implements Component {
  public function render(): Component {
    $id = $this->getDefault('id', '');
    $img = $this->get('images')[$this->getDefault('img', '')];
    $subtitle = $this->get('subtitle');
    $count = $this->getDefault('count', 0);

    return HtmlElement::create('div', [
      'id' => "dashboard-$id",
      HtmlElement::emmetBottom('.image-container>img', [
        'src' => $img,
      ]),
      HtmlElement::emmetTop('.list', [
        HtmlElement::emmetTop('.count', [$count]),
        HtmlElement::emmetTop('.subtitle', $subtitle),
      ]),
    ]);
  }

  static public function create(RawDataContainer $base, string $id, string $img, string $subtitle, int $count): self {
    return new static(array_merge($base->getData(), [
      'id' => $id,
      'img' => $img,
      'subtitle' => $subtitle,
      'count' => $count,
    ]));
  }
}


class AdminGames extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query')->assign([
      'page' => 'admin',
      'previous-page' => 'games',
    ]);

    return HtmlElement::emmetBottom('#list-games', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h1', 'Danh sách Games'),
        HtmlElement::emmetTop('button.btn-add#btn-add-game', [
          HtmlElement::emmetTop('a', [
            'href' => $urlQuery->set('subpage', 'add-game')->getUrlQuery(),
            'Thêm Game',
          ]),
        ]),
      ]),
        HtmlElement::emmetTop('.body-subpage', [
           HtmlElement::emmetTop('table#tb-games', [
            HtmlElement::emmetTop('tr.class-tr-games', [
              HtmlElement::emmetTop('th', ['ID']),
              HtmlElement::emmetTop('th', ['Tên']),
              HtmlElement::emmetBottom('th', ['Thể loại']),
              HtmlElement::emmetBottom('th', ['Mô tả']),
              HtmlElement::emmetBottom('th', ['Điều khiển']),
            ]),
          ]),
        ]),
    ]);
  }
}

class AdminAddGame extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::emmetBottom('#edit-user-page', [
      HtmlElement::emmetTop('.header-subpage-game', [
        HtmlElement::create('h2', ''),
      ]),
      HtmlElement::emmetTop('.body-subpage-game', [
         HtmlElement::emmetBottom('form#add-game-form', [
         'method' => 'GET',
         'action' => '',
         HtmlElement::emmetTop('fieldset',[
         HtmlElement::emmetBottom('legend>h2', 'Thêm game'),
           HtmlElement::create('label', 'ID'),
           HtmlElement::emmetTop('input#id-game',''),
           HtmlElement::create('label', 'Tên trò chơi'),
           HtmlElement::emmetTop('input#name-game', ''),
           HtmlElement::create('label', 'Thể loại'),
           HtmlElement::emmetTop('input#genre-game', ''),
           HtmlElement::create('label', 'Mô tả'),
           HtmlElement::emmetTop('textarea#des-game', ''),
           HtmlElement::emmetTop('button',[
             'type' => 'submit',
             'name' => 'submit',
             'Lưu'
           ]),
           HtmlElement::emmetTop('button', [
             'type' => 'reset',
             'name' => 'reset',
             'Đặt lại',
            ]),
          ]),
        ]),
      ]),
    ]);
  }
}

class AdminUsers extends RawDataContainer implements Component {
  public function render(): Component {
    $users = $this->get('db-query-set')->get('list-users')->executeOnce([], 2)->fetch();

    $children = array_map(
      function (array $userinfo) {
        [$username, $fullname] = $userinfo;
        return HtmlElement::create('tr', [
          HtmlElement::create('td', $username),
          HtmlElement::create('td', $fullname),
          HtmlElement::create('td', new AdminUserController(
            $this->set('username', $username)->getData()
          )),
        ]);
      },
      $users
    );

    return HtmlElement::emmetBottom('#user-account', [
      HtmlElement::emmetTop('#header-user-page.header-subpage', [
        HtmlElement::create('h1', 'Tài khoản User'),
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        HtmlElement::emmetTop('table#tb-games', [
          HtmlElement::emmetBottom('thead>tr.class-tr-games', [
            HtmlElement::emmetTop('th', ['Tên người dùng']),
            HtmlElement::emmetTop('th', ['Tên đầy đủ']),
            HtmlElement::emmetBottom('th', ['Điều khiển']),
          ]),
          HtmlElement::create('tbody', $children),
        ]),
        $userEditDialog,
      ]),
    ]);
  }
}

class AdminAdvanced extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::emmetBottom('div#dashboard.content',[
      'This is Advanced'
    ]);
  }
}

class AdminUserController extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query')->assign([
      'page' => 'admin',
      'username' => $this->get('username'),
      'previous-page' => 'users',
    ]);

    return HtmlElement::create('div.edit', [
      HtmlElement::emmetTop('a.edit-user', [
        'href' => $urlQuery->set('subpage', 'edit-user')->getUrlQuery(),
        'Sửa',
      ]),
      HtmlElement::emmetTop('a.delete-user', [
        'href' => $urlQuery->set('subpage', 'delete-user')->getUrlQuery(),
        'Xóa',
      ]),
    ]);
  }
}

class AdminEditUser extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $username = $urlQuery->get('username');
    $dbQuerySet = $this->get('db-query-set');
    [[$fullname]] = $dbQuerySet->get('user-info')->executeOnce([$username], 1)->fetch();

    return HtmlElement::emmetBottom('#edit-user-page', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h2', ''),
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        HtmlElement::emmetBottom('form#edit-user-form', [
          'method' => 'GET',
          'action' => '',
          HtmlElement::emmetTop('fieldset',[
            HtmlElement::emmetBottom('legend>h2',['Cập nhật người dùng']),
            HtmlElement::emmetTop('#form-group', [
            HtmlElement::emmetBottom('label', 'Tên người dùng'),
            HtmlElement::create('output', [
              $username,
            ]),
            ]),
            HtmlElement::emmetTop('#form-group', [
              HtmlElement::emmetBottom('label', 'Họ và Tên'),
              HtmlElement::create('input', [
                'type' => 'text',
                'name' => 'fullname',
                'value' => $fullname,
              ]),
            ]),
            HtmlElement::emmetTop('#form-group', [
              HtmlElement::create('label',['']),
              HtmlElement::create('button', [
                'type' => 'submit',
                'Lưu',
              ]),
            ]),
          ]),
          HiddenInputSet::instance($urlQuery->assign([
            'type' => 'action',
            'action' => 'edit-user',
            'previous-page' => 'users',
            'username' => $username,
          ])->getData()),
        ]),
      ]),
    ]);
  }
}
?>
