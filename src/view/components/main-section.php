<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/game-item.php';
require_once __DIR__ . '/footer-section.php';
require_once __DIR__ . '/player.php';
require_once __DIR__ . '/comment-section.php';
require_once __DIR__ . '/labeled-input.php';
require_once __DIR__ . '/instructed-input.php';
require_once __DIR__ . '/../../lib/utils.php';

class MainSection extends RawDataContainer implements Component {
  public function render(): Component {
    $self = $this;
    $data = $this->getData();
    $page = $data['page'];

    return HtmlElement::emmetBottom('.main.outer>.main.inner', [
      'id' => 'main-section',
      HtmlElement::create('main', [
        new MainContent($this->getData()),
      ]),
      new FooterSection(),
    ]);
  }
}

class MainContent extends RawDataContainer implements Component {
  public function render(): Component {
    $page = $this->get('page');
    $urlQuery = $this->get('url-query');

    switch ($page) {
      case 'index':
        return new GameMenu($this->getData());
      case 'genre':
        return new GameMenuByGenre($this->getData());
      case 'history':
        return new GameMenuByHistory($this->getData());
      case 'play':
        return new PlayerUserInterface(
          $this
            ->set('game-id', $urlQuery->getDefault('game-id', ''))
            ->getData()
        );
      case 'search':
        return new SearchResult($this->getData());
      case 'profile':
        return new UserProfileSetting($this->getData());
      case 'password-setting':
        return new UserPasswordSetting($this->getData());
      default:
        return new TextNode('');
    }
  }
}

class GameMenu extends RawDataContainer implements Component {
  public function render(): Component {
    $self = $this;
    $gamelist = $this->get('game-manager')->list();

    return HtmlElement::emmetTop('#game-menu', array_map(
      function (array $info) use($self) {
        [$id, $name] = $info;

        return new GameItem($self->assign([
          'game-id' => $id,
          'game-name' => $name,
        ])->getData());
      },
      $gamelist
    ));
  }
}

class GameMenuByGenre extends RawDataContainer implements Component {
  public function render(): Component {
    $self = $this;
    $urlQuery = $this->get('url-query');
    $genre = $urlQuery->getDefault('genre', '');

    $gamelist = $this
      ->get('game-genre-relationship-manager')
      ->getGames($genre)
    ;

    return HtmlElement::emmetTop('#game-menu', array_map(
      function (array $info) use($self) {
        [$id, $name] = $info;

        return new GameItem($self->assign([
          'game-id' => $id,
          'game-name' => $name,
        ])->getData());
      },
      $gamelist
    ));
  }
}

class GameMenuByHistory extends RawDataContainer implements Component {
  public function render(): Component {
    $self = $this;
    $urlQuery = $this->get('url-query');
    $genre = $urlQuery->getDefault('genre', '');

    $gamelist = $this
      ->get('user-profile')
      ->getHistory()
    ;

    $menu = HtmlElement::emmetTop('#game-menu', array_map(
      function (array $info) use($self) {
        $description = strftime('%H giờ %M phút %S — ngày %d tháng %m năm %Y', $info['date']);

        return new PlayingHistoryItem(
          $self
            ->assign($info)
            ->set('game-description', $description)
            ->getData()
        );
      },
      $gamelist
    ));

    return HtmlElement::create('div', [
      HtmlElement::emmetBottom('button#clear-history>a', [
        'href' => $urlQuery->assign([
          'type' => 'action',
          'action' => 'clear-user-history',
        ])->getUrlQuery(),
        'Làm trống Lịch sử',
      ]),
      $menu,
    ]);
  }
}

class PlayerUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    $id = $this->get('url-query')->getDefault('game-id', '');
    $info = $this->get('game-manager')->getItemInfo($id);

    if (!$info) throw new NotFoundException("Game '$id' doesn't exist");

    [
      'name' => $name,
      'genre' => $genre,
      'description' => $description,
    ] = $info;

    $commonParams = $this->assign([
      'game-id' => $id,
      'game-name' => $name,
      'game-genre' => $genre,
      'game-description' => $description,
    ]);

    return HtmlElement::create('div', [
      new Player($commonParams->getData()),
      new CommentSection($commonParams->getData()),
    ]);
  }

  static protected function requiredFieldSchema(): array {
    return array_merge(parent::requiredFieldSchema(), [
      'game-id' => 'string',
    ]);
  }
}

class SearchResult extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $search = $urlQuery->getDefault('search', '');
    $engine = $this->get('search-engine');

    if (!$search) {
      return HtmlElement::emmetTop('.error.message', 'Vui lòng nhập nội dung tìm kiếm');
    }

    $result = $engine->searchGames($search);
    $count = sizeof($result);

    if (!$result) {
      return HtmlElement::emmetTop('.error.message', [
        'Không tìm thấy trò chơi nào chứa từ khóa ',
        HtmlElement::emmetBottom('strong.search-word', $search),
      ]);
    }

    $children = array_map(
      function (array $element) {
        [
          $id,
          $name,
          $description,
        ] = $element;

        return new SearchResultItem($this->assign([
          'game-id' => $id,
          'game-name' => $name,
          'game-description' => $description,
        ])->getData());
      },
      $result
    );

    return HtmlElement::create('div', [
      HtmlElement::emmetTop('.result-count', [
        'Tìm thấy ',
        HtmlElement::emmetBottom('span.count.number.quantity', $count),
        ' trò chơi',
      ]),
      HtmlElement::emmetTop('.result-list', $children),
    ]);
  }
}

class UserProfileSetting extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    [$fullname, $username] = $this->get('user-profile')->info();

    return HtmlElement::create('div', [
      HtmlElement::emmetTop('article', [
        HtmlElement::create('h2','Thông tin cá nhân'),
        HtmlElement::create('form', [
          'method' => 'POST',
          'action' => $urlQuery->assign([
            'type' => 'action',
            'action' => 'update-user-profile',
          ])->getUrlQuery(),
          HtmlElement::emmetTop('.input-container', [
            HtmlElement::create('div', [
              HtmlElement::create('label', 'Tên đăng nhập'),
              HtmlElement::emmetTop('output#username', $username),
            ]),
            PlainLabeledInput::text('fullname', 'Họ và Tên', $fullname),
          ]),
          HtmlElement::emmetTop('.button-container', [
            HtmlElement::create('label'),
            HtmlElement::create('button', [
              'type' => 'submit',
              'Lưu',
            ]),
          ]),
        ])
      ]),
      HtmlElement::create('article', [
        HtmlElement::create('h2','Bảo Mật'),
        HtmlElement::create('form', [
          'method' => 'POST',
          'action' => $urlQuery->assign([
            'type' => 'action',
            'action' => 'update-user-password',
          ])->getUrlQuery(),
          HtmlElement::emmetTop('.input-container', [
            SecretInstructedInput::text('current-password', 'Mật khẩu hiện tại', '', ''),
            SecretInstructedInput::text('new-password', 'Mật khẩu mới', '', ''),
            SecretInstructedInput::text('re-password', 'Nhập lại Mật khẩu mới', '', ''),
          ]),
          HtmlElement::emmetTop('.button-container', [
            HtmlElement::create('label'),
            HtmlElement::create('button', [
              'type' => 'submit',
              'Lưu',
            ]),
          ]),
        ]),
      ]),
    ]);
  }
}

class PlayingHistoryItem extends RawDataContainer implements Component {
  public function render(): Component {
    return new GameItem($this->getData());
  }
}

class SearchResultItem extends RawDataContainer implements Component {
  public function render(): Component {
    return new GameItem($this->getData());
  }
}
?>
