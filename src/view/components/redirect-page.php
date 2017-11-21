<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/anchor.php';
require_once __DIR__ . '/script-embed.php';

class RedirectPage implements Component {
  private $location;

  public function __construct(string $location) {
    $this->location = $location;
  }

  public function render(): Component {
    $location = $this->location;

    return HtmlElement::create('html', [
      'lang' => 'en',
      HtmlElement::nested(['head', 'title'], 'Redirecting...'),
      HtmlElement::create('body', [
        HtmlElement::create('h1', [
          'Document has been moved to ',
          Anchor::linkify($location),
        ]),
        JavascriptEmbed::src("
          window.location.href = '$location'
        "),
      ]),
    ]);
  }
}
?>
