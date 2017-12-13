<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/template-set.php';
require_once __DIR__ . '/warning-bar.php';
require_once __DIR__ . '/toggle-favourite-button.php';
require_once __DIR__ . '/comment.php';
require_once __DIR__ . '/replying-comment-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class MainTemplateSet extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();

    return new TemplateSet([
      'warning-bar' => new WarningBar(),
      'toggle-favourite-button' => new ToggleFavouriteButton(),
      'comment-viewer' => new CommentViewer($data),
      'comment-editor' => new CommentEditor($data),
      'comment-thread-viewer' => new CommentThreadViewer($data),
      'comment-thread-editor' => new CommentThreadEditor($data),
      'replying-comment-button' => new ReplyingCommentButton($data),
    ]);
  }
}
?>
