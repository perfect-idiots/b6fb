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
    return HtmlElement::create('html', array(
      'lang' => 'en',
      HtmlElement::nested(array('head', 'title'), 'Redirecting...'),
      HtmlElement::create('body', array(
        HtmlElement::create('h1', array(
          'Document has been moved to ',
          Anchor::linkify($location),
        )),
        JavascriptEmbed::src("
          window.location.href = '$location'
        "),
      )),
    ));
  }
}
?>
