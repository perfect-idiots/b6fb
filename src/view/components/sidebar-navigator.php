<?php
require_once __DIR__ . '/base.php';

class SidebarNavigator implements Component {
  private $template, $transform, $isCurrentPage;

  public function __construct(array $template, callable $transform, callable $isCurrentPage) {
    $this->template = $template;
    $this->transform = $transform;
    $this->isCurrentPage = $isCurrentPage;
  }

  public function render(): Component {
    $transform = $this->transform;
    $isCurrentPage = $this->isCurrentPage;

    $subpageitems = array_map(
      function ($tmpl) use($transform, $isCurrentPage) {
        return HtmlElement::emmetDepth(
          "li>a",
          [
            [
              'classes' => $isCurrentPage($tmpl) ? ['current-page'] : [],
            ],

            $transform($tmpl),
          ]
        );
      },

      $this->template
    );

    return HtmlElement::emmetBottom('nav>ul', [
      HtmlElement::emmetTop('li#subpage-navigator-title.subpage.title', []),
      HtmlElement::emmetTop('ul#subpage-navigator.subpage', $subpageitems),
    ]);
  }
}
?>