<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/css-view.php';
require_once __DIR__ . '/header-section.php';
require_once __DIR__ . '/script-embed.php';
require_once __DIR__ . '/../../lib/utils.php';

class App extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();

    return HtmlElement::create('html', array(
      'lang' => 'en',
      'dataset' => array(
        'theme-name' => $data['theme-name'],
      ),
      'classes' => array(
        "theme-{$data['theme-name']}",
      ),

      HtmlElement::create('head', array(
        HtmlElement::create('meta', array('charset' => 'utf-8')),
        HtmlElement::create('title', $data['title']),
        CssView::fromFile(__DIR__ . '/../../resources/style.css', $data['colors']),
        JsonDataEmbed::dump($data['colors'], JSON_PRETTY_PRINT, array('id' => 'data-colors')),
      )),
      HtmlElement::create('body', array(
        new HeaderSection($data),
        HtmlElement::create('main'),
        HtmlElement::create('footer'),
      ))
    ));
  }
}
?>
