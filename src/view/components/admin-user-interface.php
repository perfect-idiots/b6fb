<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/../../lib/utils.php';

class AdminUserInterface extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('html', [
      'lang' => 'en',
      'classes' => ['admin'],
      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', 'Administration'),
        CssView::fromFile(__DIR__ . '/../../resources/admin.css'),
      ]),
      HtmlElement::create('body', [
        'This is admin site'
      ]),
    ]);
  }
}
?>
