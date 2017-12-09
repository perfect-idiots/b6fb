<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/comment.php';
require_once __DIR__ . '/../../lib/utils.php';

class CommentSection extends RawDataContainer implements Component {
  public function render(): Component {
    $self = $this;
    $id = $this->getDefault('game-id', '');
    $list = $this->get('comment-manager')->listByGame($id, true);
    $groups = $list['groups'];

    $commentEditorContainer = HtmlElement::create('comment-editor-container');

    $threadContainer = HtmlElement::create('article', array_map(
      function (array $thread) use($self) {
        return new CommentThreadViewer($self->assign([
          'top' => $thread['top'],
          'replies' => $thread['replies'],
        ])->getData());
      },
      $groups
    ));

    return HtmlElement::create('div', [
      $commentEditorContainer,
      $threadContainer,
    ]);
  }

  static protected function requiredFieldSchema(): array {
    return array_merge(parent::requiredFieldSchema(), [
      'comment-manager' => 'CommentManager',
    ]);
  }
}
?>
