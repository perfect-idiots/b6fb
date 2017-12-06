<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/swf-embed.php';
require_once __DIR__ . '/markdown-view.php';
require_once __DIR__ . '/../../lib/utils.php';

class Player extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $id = $this->get('game-id');
    $name = $this->get('game-name');
    $description = $this->get('game-description');
    $isFavourite = $this->get('user-profile')->checkFavourite($id);

    return HtmlElement::create('article', [
      'dataset' => [
        'game-id' => $id,
        'game-name' => $name,
      ],
      'classes' => [
        $isFavourite ? 'favourite' : '',
      ],
      HtmlElement::emmetTop('.embed-container', [
        SwfEmbed::id($urlQuery, $id),
      ]),
      HtmlElement::emmetTop('.info', [
        HtmlElement::emmetTop('h2.subtitle.game-name', $name),
        HtmlElement::emmetTop('.control', []),
        HtmlElement::emmetTop('.description', MarkdownView::instance($description)),
      ]),
    ]);
  }

  static protected function requiredFieldSchema(): array {
    return array_merge(parent::requiredFieldSchema(), [
      'game-id' => 'string',
      'game-name' => 'string',
      'game-description' => 'string',
    ]);
  }
}
?>
