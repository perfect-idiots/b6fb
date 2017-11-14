<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class NavigatorSection extends RawDataContainer implements Component {
  public function render(): Component {
    $subpagetmpls = $this->get('subpages');

    $subpageitems = array_map(
      function ($tmpl) {
        return HtmlElement::emmetDepth(
          "li#to-{$tmpl['page']}>a",
          [
            [
              'dataset' => $tmpl,
            ],
            [
              'href' => $tmpl['href'],
              $tmpl['title'],
            ],
          ]
        );
      },

      $subpagetmpls
    );

    return HtmlElement::emmetBottom('nav>ul', [
      HtmlElement::emmetTop('li#subpage-navigator-title.subpage.title', 'Navigation'),
      HtmlElement::emmetTop('ul#subpage-navigator.subpage', $subpageitems),
    ]);
  }
}
?>
