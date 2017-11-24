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

    return HtmlElement::create('main', [
      'id' => 'main-section',
      new MainContent($this->getData()),
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
      case 'play':
        return new PlayerUserInterface(
          $this
            ->set('game-id', $urlQuery->getDefault('game-id', ''))
            ->getData()
        );
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

class PlayerUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    $id = $this->get('url-query')->getDefault('game-id', '');
    $info = $this->get('game-manager')->getItemInfo($id);

    if (!sizeof($info)) throw new NotFoundException("Game '$id' doesn't exist");
    [[$name, $genre, $description]] = $info;

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
?>
