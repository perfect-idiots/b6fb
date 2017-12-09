<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/text-area-element.php';
require_once __DIR__ . '/../../lib/utils.php';

abstract class Comment extends RawDataContainer implements Component {}

class CommentViewer extends Comment {
  public function render(): Component {
    $fullname = $this->getDefault('author-fullname', '');
    $username = $this->getDefault('author-username', '');
    $content = $this->getDefault('comment-content', '');
    $parent = $this->getDefault('comment-parent', false);
    $colors = $this->get('colors');
    $avatar = $this->get('images')["default-avatar-{$colors['text-color']}-image"];

    return HtmlElement::emmetTop('article.comment.view', [
      'dataset' => [
        'parent' => $parent,
      ],
      HtmlElement::emmetTop('comment-image', [
        HtmlElement::emmetTop('img.author.avatar', [
          'src' => $avatar,
          'alt' => 'Avatar',
        ]),
      ]),
      HtmlElement::create('comment-text', [
        HtmlElement::emmetTop('comment-author-identity.author.identity', [
          HtmlElement::emmetBottom('comment-author-fullname>span.fullname', $fullname),
          HtmlElement::emmetBottom('comment-author-username>span.username', "@$username"),
        ]),
        HtmlElement::emmetBottom('comment-content>p.content', $content),
      ]),
    ]);
  }
}

class CommentEditor extends Comment {
  public function render(): Component {
    $content = $this->getDefault('comment-content', '');
    $colors = $this->get('colors');
    $avatar = $this->get('images')["default-avatar-{$colors['text-color']}-image"];

    return HtmlElement::create('comment-editor', [
      HtmlElement::emmetTop('comment-image', [
        HtmlElement::emmetTop('img.author.avatar', [
          'src' => $avatar,
          'alt' => 'Avatar',
        ]),
      ]),
      HtmlElement::emmetTop('comment-text', [
        HtmlElement::emmetBottom('comment-content>.input-container', [
          new TextAreaElement([
            'classes' => [
              'content',
              'editor',
            ],
            'style' => [
              'resize' => 'none',
            ],
            $content,
          ]),
        ]),
        HtmlElement::emmetBottom('comment-control>.button-container', [
          HtmlElement::emmetTop('button.submit', 'Xác nhận'),
          HtmlElement::emmetTop('button.cancel', 'Hủy bỏ'),
        ]),
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
      'author-username' => $wrapper->getDefault('author-id', ''),
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
