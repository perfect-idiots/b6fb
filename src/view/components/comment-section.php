<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class CommentSection extends RawDataContainer implements Component {
  public function render(): Component {
    $id = $this->get('game-id');
    $name = $this->get('game-name');

    return HtmlElement::create('article', [
      'dataset' => [
        'game-id' => $id,
        'game-name' => $name,
      ],
    ]);
  }

  static protected function requiredFieldSchema(): array {
    return array_merge(parent::requiredFieldSchema(), [
      'game-id' => 'string',
      'game-name' => 'string',
    ]);
  }
}
?>
