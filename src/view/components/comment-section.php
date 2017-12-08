<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class CommentSection extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('article');
  }
}
?>
