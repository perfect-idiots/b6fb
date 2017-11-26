<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class GameItem extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $id = $this->get('game-id');

    return HtmlElement::create('a', [
      'href' => $urlQuery->assign([
        'type' => 'html',
        'page' => 'play',
        'game-id' => $id,
      ])->getUrlQuery(),
      HtmlElement::emmetBottom('article>figure', [
        HtmlElement::create('img', [
          'src' => $urlQuery->assign([
            'type' => 'file',
            'mime' => 'image/jpeg',
            'name' => $this->get('game-id'),
            'purpose' => 'game-img',
          ])->getUrlQuery(),
        ]),
        HtmlElement::create('figcaption', [
          $this->get('game-name'),
        ]),
      ]),
    ]);
  }
}
?>
