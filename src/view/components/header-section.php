<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/logo.php';
require_once __DIR__ . '/search-box.php';
require_once __DIR__ . '/theme-switcher.php';
require_once __DIR__ . '/text-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class HeaderSection extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('header', [
      'id' => 'main-header',
      'classes' => ['header'],
      HtmlElement::emmetBottom('.left-corner.segment', [
        new Logo($this->getData()),
      ]),
      HtmlElement::emmetBottom('.middle-segment.segment', [
        new SearchBox($this->getData()),
      ]),
      HtmlElement::emmetBottom('.right-corner.segment', [
        new ThemeSwitcher($this->getData()),
        new PrimaryButton('Login'),
      ]),
    ]);
  }
}
?>
