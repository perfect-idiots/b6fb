<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/parsedown.php';

class MarkdownView implements Component {
  private $html;

  public function __construct(Parsedown $parsedown, string $markdown) {
    $this->html = $parsedown->text($markdown);
  }

  public static function instance(string $markdown): self {
    return new self(Parsedown::instance(), $markdown);
  }

  public function render(): Component {
    return new UnescapedText($this->html);
  }
}
?>
