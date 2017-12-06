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
?>
