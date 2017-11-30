<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/anchor.php';
require_once __DIR__ . '/header-section.php';
require_once __DIR__ . '/sidebar-navigator.php';
require_once __DIR__ . '/hidden-input.php';
require_once __DIR__ . '/labeled-input.php';
require_once __DIR__ . '/markdown-view.php';
require_once __DIR__ . '/../../lib/utils.php';

class AdminUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $login = $this->get('login');
    $username = $login->username();
    $isLoggedIn = $login->isLoggedIn();
    $cssFileName = $isLoggedIn ? 'admin' : 'login';
    $images = $this->get('images');

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
        HtmlElement::create('title', 'Quản trị'),
        CssView::fromFile(__DIR__ . "/../../resources/$cssFileName.css"),
      ]),
      $isLoggedIn
        ? HtmlElement::create('body', [
          HtmlElement::emmetBottom('header#main-header', [
            HtmlElement::emmetTop('a#title-header', [
              'style' => [
                'padding-left' => '10px',
              ],
              'href' => $urlQuery->set('page', 'admin')->getUrlQuery(),
              'Quản trị',
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
      [[[], $subpagetmpls]],
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
      case 'delete-user':
        return new AdminDeleteUser($data);
      case 'add-game':
        return new AdminAddGame($data);
      case 'reset-database':
        return new AdminResetDatabase($data);
      case 'change-admin-password':
        return new AdminChangePassword($data);
      default:
        throw new NotFoundException();
    }
  }
}

class AdminDashboard extends RawDataContainer implements Component {
  public function render(): Component {
    $images = $this->get('images');
    $gameManager = $this->get('game-manager');
    $userManager = $this->get('user-manager');

    return HtmlElement::emmetBottom('#dashboard', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h1', 'Bảng điều khiển'),
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        DashboardPanel::create($this, 'games', 'gamepad-image', 'Trò chơi', $gameManager->count()),
        DashboardPanel::create($this, 'users', 'multi-users-image', 'Người dùng', $userManager->count()),
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
    $urlQuery = $this->get('url-query');

    return HtmlElement::create('a', [
      'id' => "dashboard-$id",
      'href' => $urlQuery->set('subpage', $id)->getUrlQuery(),
      HtmlElement::emmetBottom('.image-container>img', [
        'src' => $img,
      ]),
      HtmlElement::emmetTop('.list', [
        HtmlElement::emmetTop('.count', [$count . ' ']),
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

    $games = $this->get('game-manager')->list();
    $genres = $this->get('genre-manager')->list();

    $listgame = array_map(
      function (array $userinfo) use($urlQuery) {
        [
          'id' => $id,
          'name' => $name,
          'genre' => $genre,
        ] = $userinfo;

        return HtmlElement::create('tr', [
          HtmlElement::create('td', $id),
          HtmlElement::create('td', $name),
          HtmlElement::create('td', implode(', ', array_values($genre))),
          HtmlElement::create('td', new AdminEditDeletePair($urlQuery, 'edit-game', 'delete-game')),
        ]);
      },
      $games
    );

    $listgenres = array_map(
      function (array $genreinfo) use($urlQuery) {
        [
          'id' => $id,
          'name' => $name,
        ] = $genreinfo;

        return HtmlElement::create('tr', [
          HtmlElement::create('td', $id),
          HtmlElement::create('td', $name),
          HtmlElement::create('td', new AdminEditDeletePair($urlQuery, 'edit-genre', 'delete-genre')),
        ]);
      },
      $genres
    );

    return HtmlElement::emmetTop('#list-games', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h1', 'Quản lý Trò chơi'),
        HtmlElement::emmetTop('button.btn-add#btn-add-genre', [
          HtmlElement::emmetTop('a', [
            'href' => '',
            'Thêm thể loại',
          ]),
        ]),
        HtmlElement::emmetTop('button.btn-add#btn-add-game', [
          HtmlElement::emmetTop('a', [
            'href' => $urlQuery->set('subpage', 'add-game')->getUrlQuery(),
            'Thêm trò chơi',
          ]),
        ]),
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        HtmlElement::emmetTop('article.genre-list-container', [
          HtmlElement::create('h2', 'Danh sách Thể loại'),
          HtmlElement::emmetTop('table#genre-list', [
            HtmlElement::emmetBottom('thead>tr.class-tr-genres', [
            HtmlElement::create('th', ['ID']),
            HtmlElement::create('th', ['Tên']),
            HtmlElement::create('th', ['Điều khiển']),
          ]),
            HtmlElement::create('tbody', $listgenres),
          ]),
        ]),
        HtmlElement::emmetTop('article.game-list-container', [
          HtmlElement::create('h2', 'Danh sách Trò chơi'),
          HtmlElement::emmetTop('table#game-list', [
            HtmlElement::emmetBottom('thead>tr.class-tr-games', [
            HtmlElement::create('th', ['ID']),
            HtmlElement::create('th', ['Tên']),
            HtmlElement::create('th', ['Thể loại']),
            HtmlElement::create('th', ['Điều khiển']),
          ]),
            HtmlElement::create('tbody', $listgame),
          ]),
        ]),
      ]),
    ]);
  }
}

class AdminAddGame extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::emmetTop('#edit-game-page', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h2','Thêm game'),
      ]),
      HtmlElement::emmetBottom('.body-subpage-game>form#add-game-form', [
        'method' => 'GET',
        'action' => '',
        HtmlElement::emmetTop('fieldset#input-container', [
          PlainLabeledInput::text('game-id', 'ID'),
          PlainLabeledInput::text('game-name', 'Tên trò chơi'),
          PlainLabeledInput::text('game-genre', 'Thể loại'),
          LabeledTextArea::text('game-description', 'Mô tả'),
          LabeledFileInput::text('game-swf', 'Tệp trò chơi'),
          LabeledFileInput::text('game-image', 'Tệp hình ảnh'),
          HtmlElement::create('button',[
            'type' => 'submit',
            'name' => 'submit',
            'Lưu'
          ]),
          HtmlElement::create('button', [
            'type' => 'reset',
            'name' => 'reset',
            'Đặt lại',
          ]),
        ]),
      ]),
    ]);
  }
}

class AdminUsers extends RawDataContainer implements Component {
  public function render(): Component {
    $users = $this->get('user-manager')->list();
    $urlQuery = $this->get('url-query')->assign([
      'page' => 'admin',
      'previous-page' => 'users',
    ]);

    $children = array_map(
      function (array $userinfo) use($urlQuery) {
        [$username, $fullname] = $userinfo;
        return HtmlElement::create('tr', [
          HtmlElement::create('td', $username),
          HtmlElement::create('td', $fullname),
          HtmlElement::create('td', new AdminEditDeletePair(
            $urlQuery->set('username', $username),
            'edit-user',
            'delete-user'
          )),
        ]);
      },
      $users
    );

    return HtmlElement::emmetTop('#user-account', [
      HtmlElement::emmetTop('#header-user-page.header-subpage', [
        HtmlElement::create('h1', 'Tài khoản người dùng'),
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        HtmlElement::emmetTop('table#tb-games', [
          HtmlElement::emmetBottom('thead>tr.class-tr-games', [
            HtmlElement::create('th', ['Tên người dùng']),
            HtmlElement::create('th', ['Tên đầy đủ']),
            HtmlElement::create('th', ['Điều khiển']),
          ]),
          HtmlElement::create('tbody', $children),
        ]),
      ]),
    ]);
  }
}

class AdminAdvanced extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();

    return HtmlElement::emmetBottom('div#dashboard.content', [
      HtmlElement::emmetBottom('.header-subpage>h1', 'Nâng cao'),
      new AdminAdvancedAdminManagementSection($data),
      new AdminAdvancedResetDatabaseSection($data),
    ]);
  }
}

class AdminAdvancedResetDatabaseSection extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');

    return HtmlElement::emmetTop('article', [
      HtmlElement::create('h2', 'Reset và Khởi tạo'),
      HtmlElement::create('form', [
        'method' => 'GET',
        HtmlElement::emmetTop('.input-container', [
          LabeledCheckbox::text('game', 'Dữ liệu Trò chơi'),
          LabeledCheckbox::text('user', 'Dữ liệu Người dùng'),
          LabeledCheckbox::text('admin', 'Dữ liệu Người quản trị'),
        ]),
        HtmlElement::emmetTop('.button-container', [
          HtmlElement::create('button', [
            'type' => 'confirm',
            'Xóa và Đặt lại CSDL',
          ]),
        ]),
        new HiddenInputSet(
          $urlQuery
            ->set('subpage', 'reset-database')
            ->getData()
        ),
      ]),
    ]);
  }
}

class AdminAdvancedAdminManagementSection extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');

    return HtmlElement::emmetTop('article', [
      HtmlElement::create('h2', 'Quản lý Tài khoản Quản trị'),
      HtmlElement::create('div', [
        HtmlElement::create('ul', [
          HtmlElement::emmetBottom('li>a', [
            'href' => $urlQuery->set('subpage', 'change-admin-password')->getUrlQuery(),
            'Đổi mật khẩu',
          ]),
        ]),
      ]),
    ]);
  }
}

class AdminChangePassword extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');

    return  HtmlElement::emmetTop('#update-password-account', [
      HtmlElement::emmetTop('#header-user-page.header-subpage', [
        HtmlElement::create('h1', 'Thay đổi mật khẩu'),
      ]),
      HtmlElement::emmetBottom('.body-subpage>form', [
        'method' => 'POST',
        'action' => $urlQuery->assign([
          'type' => 'action',
          'action' => 'update-admin-password',
        ])->getUrlQuery(),
        HtmlElement::emmetTop('.input-container', [
          SecretLabeledInput::text('current-password', 'Mật khẩu hiện tại'),
          SecretLabeledInput::text('new-password', 'Mật khẩu mới'),
          SecretLabeledInput::text('re-password', 'Nhập lại Mật khẩu mới'),
        ]),
        HtmlElement::emmetTop('.button-container', [
          HtmlElement::create('button', [
            'type' => 'submit',
            'Lưu',
          ]),
          HtmlElement::create('button', [
            'type' => 'reset',
            'Nhập lại',
          ]),
          HtmlElement::emmetBottom('button>a.back', [
            'href' => $urlQuery
              ->without(['current-password', 'new-password', 're-password'])
              ->set('subpage', 'advanced')
              ->getUrlQuery()
            ,
            'Quay lại',
          ]),
        ]),
      ]),
    ]);
  }
}

class AdminEditUser extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $username = $urlQuery->get('username');
    $fullname = $this->get('user-manager')->getUserFullname($username);

    return HtmlElement::emmetBottom('#edit-user-page', [
      HtmlElement::emmetTop('.header-subpage', [
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        HtmlElement::emmetBottom('form#edit-user-form', [
          'method' => 'GET',
          'action' => '',
          HtmlElement::emmetTop('fieldset',[
            HtmlElement::emmetBottom('legend>h2',['Cập nhật người dùng']),
            HtmlElement::emmetTop('#form-group', [
              HtmlElement::create('label', 'Tên người dùng'),
              HtmlElement::create('output', [
                $username,
              ]),
            ]),
            HtmlElement::emmetTop('#form-group', [
              HtmlElement::create('label', 'Họ và Tên'),
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

class AdminDeleteUser extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $username = $urlQuery->get('username');

    return HtmlElement::emmetTop('#delete-user-page', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h2', ''),
      ]),
      HtmlElement::emmetBottom('.body-subpage>#delete-user-page', [
        HtmlElement::emmetTop('.question', [
          'Bạn có thực muốn xóa người dùng',
          HtmlElement::emmetTop('span.username', $username),
          '?',
        ]),
        HtmlElement::emmetTop('.button-container', [
          HtmlElement::emmetTop('a#delete', [
            'href' => $urlQuery->assign([
              'type' => 'action',
              'action' => 'delete-user',
              'previous-page' => 'users',
              'username' => $username,
            ])->getUrlQuery(),
            'Xóa'
          ]),
          HtmlElement::emmetTop('a#cancel', [
            'href' => $urlQuery->assign([
              'type' => 'html',
              'page' => 'admin',
              'subpage' => 'users',
            ])->getUrlQuery(),
            'Quay lại'
          ]),
        ]),
      ]),
    ]);
  }
}

class AdminResetDatabase extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');

    return HtmlElement::emmetTop('#reset-database', [
      HtmlElement::emmetBottom('.header-subpage>h1', 'Xóa và Đặt Lại Cơ sở dữ liệu'),
      new AdminWarningBox('Thao tác sau đây sẽ đặt lại CSDL. Hành động này **không thể hoàn tác**.'),
      HtmlElement::emmetBottom('.question>strong>h3', 'Bạn có muốn tiếp tục?'),
      HtmlElement::emmetBottom('.answer>form', [
        'method' => 'POST',
        'action' => $urlQuery->assign([
          'type' => 'action',
          'action' => 'reset-database',
        ])->getUrlQuery(),
        HtmlElement::emmetTop('.input-container', [
          SecretLabeledInput::text('password', 'Mật khẩu Admin'),
        ]),
        HtmlElement::emmetTop('.button-container', [
          HtmlElement::emmetTop('button.confirm.dangerous', [
            'type' => 'confirm',
            'Xóa và Đặt lại CSDL',
          ]),
          HtmlElement::emmetBottom('button.cancel.safe>a', [
            'href' => $urlQuery->set('subpage', 'advanced')->getUrlQuery(),
            'Quay lại',
          ]),
        ]),
        new HiddenInputSet(
          $urlQuery->set('confirmed', 'on')->getData()
        ),
      ]),
    ]);
  }
}

class AdminEditDeletePair implements Component {
  private $urlQuery, $edit, $delete;

  public function __construct(UrlQuery $urlQuery, string $edit, string $delete) {
    $this->urlQuery = $urlQuery;
    $this->edit = $edit;
    $this->delete = $delete;
  }

  public function render(): Component {
    return HtmlElement::create('div', [
      new Anchor(
        $this->urlQuery->set('subpage', $this->edit)->getUrlQuery(),
        ['Sửa']
      ),
      new Anchor(
        $this->urlQuery->set('subpage', $this->delete)->getUrlQuery(),
        ['Xóa']
      ),
    ]);
  }
}

class AdminWarningBox implements Component {
  private $content;

  public function __construct(string $content) {
    $this->content = $content;
  }

  public function render(): Component {
    return HtmlElement::emmetTop('.warning', [
      MarkdownView::indented("⚠ **Cảnh báo:** $this->content"),
    ]);
  }
}
?>
