<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/header-section.php';
require_once __DIR__ . '/navigator-section.php';
require_once __DIR__ . '/main-section.php';
require_once __DIR__ . '/script-embed.php';
require_once __DIR__ . '/footer-section.php';
require_once __DIR__ . '/../../lib/utils.php';

class App extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();
    $cssVars = array_merge($data['colors'], $data['sizes'], $data['images']);
    $login = $data['login'];

    return HtmlElement::create('html', [
      'lang' => 'en',
      'dataset' => [
        'theme-name' => $data['theme-name'],
        'username' => $login->username(),
      ],
      'classes' => [
        "theme-{$data['theme-name']}",
        $login->isLoggedIn() ? 'logged-in' : 'anonymous',
      ],

      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', $data['title']),
        CssView::fromFile(__DIR__ . '/../../resources/style.css', $cssVars),
        JsonDataEmbed::dump($data['cookie']->getData(), JSON_PRETTY_PRINT, ['id' => 'data-cookie']),
        JsonDataEmbed::dump($data['colors'], JSON_PRETTY_PRINT, ['id' => 'data-colors']),
        JsonDataEmbed::dump($data['sizes'], JSON_PRETTY_PRINT, ['id' => 'data-sizes']),
        JsonDataEmbed::dump($data['images'], JSON_PRETTY_PRINT, ['id' => 'data-images']),
      ]),
      HtmlElement::create('body', [
        new HeaderSection($data),
        new NavigatorSection($data),
        new MainSection($data),
      ]),
      JavascriptEmbed::file(__DIR__ . '/../../resources/scripts/script.js'),
    ]);
  }
}
?>
