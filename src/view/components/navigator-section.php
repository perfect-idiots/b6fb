<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class NavigatorSection extends RawDataContainer implements Component {
  public function render(): Component {
    $currentpage = $this->get('page');
    $subpagetmpls = $this->get('subpages');

    $subpageitems = array_map(
      function ($tmpl) use($currentpage) {
        $page = $tmpl['page'];

        return HtmlElement::emmetDepth(
          "li#to-$page>a",
          [
            [
              'dataset' => $tmpl,
              'classes' => $currentpage === $page ? ['current-page'] : [],
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
      HtmlElement::emmetTop('li#subpage-navigator-title.subpage.title', []),
      HtmlElement::emmetTop('ul#subpage-navigator.subpage', $subpageitems),
    ]);
  }
}
?>
