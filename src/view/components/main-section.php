<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/game-item.php';
require_once __DIR__ . '/footer-section.php';
require_once __DIR__ . '/player.php';
require_once __DIR__ . '/comment-section.php';
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
      case 'play':
        return new PlayerUserInterface(
          $this
            ->set('game-id', $urlQuery->getDefault('game-id', ''))
            ->getData()
        );
      case 'search':
        return new SearchResult($this->getData());
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

class PlayerUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    $id = $this->get('url-query')->getDefault('game-id', '');
    $info = $this->get('game-manager')->getItemInfo($id);

    if (!$info) throw new NotFoundException("Game '$id' doesn't exist");

    [
      'name' => $name,
      'genre-ids' => $genreIDs,
      'genre-names' => $genreNames,
      'description' => $description,
    ] = $info;

    $commonParams = $this->assign([
      'game-id' => $id,
      'game-name' => $name,
      'game-genre-ids' => $genreIDs,
      'game-genre-names' => $genreNames,
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

class SearchResultItem extends RawDataContainer implements Component {
  public function render(): Component {
    return new GameItem($this->getData());
  }
}
?>
