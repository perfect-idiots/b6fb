<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

abstract class Comment extends RawDataContainer implements Component {}

class CommentViewer extends Comment {
  public function render(): Component {
    $fullname = $this->getDefault('author-fullname', '');
    $username = $this->getDefault('author-username', '');
    $content = $this->getDefault('comment-content', '');
    $avatar = $this->get('images')['default-avatar-image'];

    return HtmlElement::emmetTop('article.comment.view', [
      HtmlElement::emmetTop('figure.author', [
        HtmlElement::emmetTop('img.avatar', [
          'src' => $avatar,
          'alt' => 'Avatar',
        ]),
        HtmlElement::emmetTop('figcaption.identity', [
          HtmlElement::emmetTop('span.fullname', $fullname),
          HtmlElement::emmetTop('span.username', $username),
        ]),
      ]),
      HtmlElement::create('comment-content', $content),
    ]);
  }
}

class CommentEditor extends Comment {
  public function render(): Component {
    $content = $this->getDefault('comment-content', '');

    return HtmlElement::create('comment-editor', [
      HtmlElement::emmetBottom('comment-content>.input-container>textarea.content.editor', $content),
      HtmlElement::emmetBottom('comment-control>.button-container', [
        HtmlElement::emmetTop('button.submit', 'Xác nhận'),
        HtmlElement::emmetTop('button.cancel', 'Hủy bỏ'),
      ]),
    ]);
  }
}
?>
