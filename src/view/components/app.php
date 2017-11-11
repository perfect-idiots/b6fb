<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/meta-element.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/header-section.php';
require_once __DIR__ . '/main-section.php';
require_once __DIR__ . '/script-embed.php';
require_once __DIR__ . '/../../lib/utils.php';

class App extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();

    return HtmlElement::create('html', [
      'lang' => 'en',
      'dataset' => [
        'theme-name' => $data['theme-name'],
      ],
      'classes' => [
        "theme-{$data['theme-name']}",
      ],

      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        HtmlElement::create('title', $data['title']),
        CssView::fromFile(__DIR__ . '/../../resources/style.css', array_merge(data['colors'], data['sizes'])),
        JsonDataEmbed::dump($data['cookie']->getData(), JSON_PRETTY_PRINT, ['id' => 'data-cookie']),
        JsonDataEmbed::dump($data['colors'], JSON_PRETTY_PRINT, ['id' => 'data-colors']),
        JsonDataEmbed::dump($data['sizes'], JSON_PRETTY_PRINT, ['id' => 'data-sizes']),
      ]),
      HtmlElement::create('body', [
        new HeaderSection($data),
        new MainSection($data),
        HtmlElement::create('footer'),
      ])
    ]);
  }
}
?>
