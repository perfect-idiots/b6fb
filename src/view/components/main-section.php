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
    $gamelist = $data['game-manager']->list();

    return HtmlElement::create('main', [
      'id' => 'main-section',
      HtmlElement::emmetTop('#game-menu', array_map(
        function (array $info) use($self) {
          [$id, $name] = $info;

          return new GameItem($self->assign([
            'game-id' => $id,
            'game-name' => $name,
          ])->getData());
        },
        $gamelist
      )),
      new FooterSection(),
    ]);
  }
}
?>
