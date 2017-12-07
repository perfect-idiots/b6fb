<?php
require_once __DIR__ . '/base.php';

class WarningBar implements Component {
  private $message;

  public function __construct($message = '') {
    $this->message = $message;
  }

  public function render(): Component {
    return HtmlElement::emmetBottom('div>p', [
      HtmlElement::emmetTop('span.title', MarkdownView::instance('⚠ **Cảnh báo:** ')),
      HtmlElement::emmetTop('span.message', $this->message),
      HtmlElement::emmetTop('button.close', '⨉'),
    ]);
  }
}

class WarningBarContainer implements Component {
  private $children;

  public function __construct(array $children = []) {
    $this->children = array_map(
      function ($child) {
        return $child instanceof WarningBar
          ? $child
          : new WarningBar($child)
        ;
      },
      $children
    );
  }

  public function render(): Component {
    return HtmlElement::create('warning-bar-container', $this->children);
  }
}
?>
