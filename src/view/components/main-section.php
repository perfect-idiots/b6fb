<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/game-item.php';
require_once __DIR__ . '/footer-section.php';
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

    switch ($page) {
      case 'index':
        return new GameMenu($this->getData());
      case 'play':
        return new TextNode('play');
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
?>
