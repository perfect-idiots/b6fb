<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

abstract class Comment extends RawDataContainer implements Component {}

class CommentViewer extends Comment {
  public function render(): Component {
    $fullname = $this->getDefault('author-fullname', '');
    $username = $this->getDefault('author-username', '');
    $content = $this->getDefault('comment-content', '');
    $parent = $this->getDefault('comment-parent', false);
    $avatar = $this->get('images')['default-avatar-image'];

    return HtmlElement::emmetTop('article.comment.view', [
      'dataset' => [
        'parent' => $parent,
      ],
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

abstract class CommentThread extends RawDataContainer implements Component {
  abstract protected function commentComponentName(): string;

  public function render(): Component {
    $self = $this;
    $component = $this->commentComponentName();
    $top = $this->getDefault('top', []);
    $replies = $this->getDefault('replies', []);
    $topId = RawDataContainer::instance($top)->getDefault('id', -1);

    return HtmlElement::create('article', [
      HtmlElement::create('surface-comment-container', [
        new $component($this->createCommentParams($top)),
      ]),
      HtmlElement::create('replying-comment-container', array_map(
        function (array $response) use($self, $topId) {
          return new CommentViewer(
            $self
              ->set('comment-parent', $topId)
              ->createCommentParams($response)
          );
        },
        $replies
      )),
    ]);
  }

  private function createCommentParams(array $data): array {
    $wrapper = new RawDataContainer($data);

    return $this->assign([
      'author-fullname' => $wrapper->getDefault('author-fullname', ''),
      'author-username' => $wrapper->getDefault('author-username', ''),
      'comment-content' => $wrapper->getDefault('content', ''),
      'comment-parent' => $wrapper->getDefault('parent-comment-id', -1),
    ])->getData();
  }
}

class CommentThreadViewer extends CommentThread {
  protected function commentComponentName(): string {
    return 'CommentViewer';
  }
}

class CommentThreadEditor extends CommentThread {
  protected function commentComponentName(): string {
    return 'CommentEditor';
  }
}
?>
