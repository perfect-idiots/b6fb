<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/header-section.php';
require_once __DIR__ . '/sidebar-navigator.php';
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
      default:
        throw new NotFoundException();
    }
  }
}

class AdminDashboard extends RawDataContainer implements Component {
  public function render(): Component {
    $images = $this->get('images');

    return HtmlElement::emmetBottom('#dashboard', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h1', 'Dashboard'),
      ]),
      HtmlElement::emmetTop('.body-subpage', [
        DashboardPanel::create($this, 'game', 'gamepad-image', 'Trò chơi', 12),
        DashboardPanel::create($this, 'user', 'multi-users-image', 'Người dùng', 34),
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
    return HtmlElement::emmetBottom('#list-games', [
      HtmlElement::emmetTop('.header-subpage', [
        HtmlElement::create('h1', 'Danh sách Games'),
        HtmlElement::emmetTop('button.btn-add#btn-add-game', [
          HtmlElement::emmetTop('a', [
            'href' =>'',
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
              HtmlElement::emmetBottom('th', ['Điều khiển'])
            ]),
          ]),
        ]),
    ]);
  }
}

class AdminUsers extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::emmetBottom('#user-account', [
      HtmlElement::emmetTop('#header-user-page.header-subpage', [
        HtmlElement::create('h1', 'Tài khoản User'),
        HtmlElement::emmetTop('button.btn-add#btn-add-user', [
          HtmlElement::emmetTop('a', [
            'href' =>'',
            'Thêm User',
          ]),
        ]),
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
?>
