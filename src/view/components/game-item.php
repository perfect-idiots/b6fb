<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class GameItem extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query')->assign([
      'type' => 'file',
      'mime' => 'image/jpeg',
      'name' => $this->get('game-id'),
      'purpose' => 'game-img',
    ]);

    return HtmlElement::emmetBottom('article>figure', [
      HtmlElement::create('img', [
        'src' => $urlQuery->getUrlQuery(),
      ]),
      HtmlElement::create('figcaption', [
        $this->get('game-name'),
      ]),
    ]);
  }
}
?>
