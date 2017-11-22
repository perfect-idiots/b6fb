<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/sidebar-navigator.php';
require_once __DIR__ . '/../../lib/utils.php';

class NavigatorSection extends RawDataContainer implements Component {
  public function render(): Component {
    $currentpage = $this->get('page');
    $subpagetmpls = $this->get('subpages');

    return new SidebarNavigator(
      [
        ['', $subpagetmpls],
      ],
      function ($tmpl) {
        return [
          'href' => $tmpl['href'],
          $tmpl['title']
        ];
      },
      function ($tmpl) use($currentpage) {
        return $tmpl['page'] === $currentpage;
      }
    );
  }
}
?>
