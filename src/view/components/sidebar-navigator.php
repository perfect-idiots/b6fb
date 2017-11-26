<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class SidebarNavigator implements Component {
  private $template, $transform, $isCurrentPage;

  public function __construct(array $template, callable $transform, callable $isCurrentPage) {
    $this->template = $template;
    $this->transform = $transform;
    $this->isCurrentPage = $isCurrentPage;
  }

  public function render(): Component {
    $template = $this->template;
    $transform = $this->transform;
    $isCurrentPage = $this->isCurrentPage;

    $children = [];

    foreach ($template as [$subtitle, $submenu]) {
      $subtmpl = [
        'template' => $submenu,
        'transform' => $transform,
        'is-current-page' => $isCurrentPage,
      ];

      array_push(
        $children,
        new SidebarNavigatorSubtitle($subtitle),
        new SidebarNavigatorSubmenu($subtmpl)
      );
    }

    return HtmlElement::emmetBottom('nav>ul', $children);
  }
}

class SidebarNavigatorSubtitle implements Component {
  private $subtitle;

  public function __construct($subtitle) {
    $this->subtitle = $subtitle;
  }

  public function render(): Component {
    return HtmlElement::emmetTop('li#subpage-navigator-title.subpage.title', $this->subtitle);
  }
}

class SidebarNavigatorSubmenu extends RawDataContainer implements Component {
  static protected function verifyFields(array $data): void {
    parent::verifyFields($data);
    foreach (['transform', 'is-current-page'] as $key) {
      if (!is_callable($data[$key])) throw new TypeError("Field '$key' is not callable");
    }
  }

  static protected function requiredFieldSchema(): array {
    return array_merge(parent::requiredFieldSchema(), [
      'template' => 'array',
      'transform' => '',
      'is-current-page' => '',
    ]);
  }

  public function render(): Component {
    $template = $this->get('template');
    $transform = $this->get('transform');
    $isCurrentPage = $this->get('is-current-page');

    $children = array_map(
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

      $template
    );

    return HtmlElement::emmetTop('ul#subpage-navigator.subpage', $children);
  }
}
?>
