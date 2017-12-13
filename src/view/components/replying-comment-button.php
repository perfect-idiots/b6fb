<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class ReplyingCommentButton extends RawDataContainer implements Component {
  public function render(): Component {
    $targetedCommentId = $this->getDefault('targeted-comment-id', false);

    return HtmlElement::emmetBottom('replying-comment-button.outer>a.inner', [
      'dataset' => [
        'targeted-comment-id' => $targetedCommentId,
      ],
      'style' => [
        'text-decoration' => 'underline',
        'cursor' => 'pointer',
      ],
      'Trả lời',
    ]);
  }
}
?>
