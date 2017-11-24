<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/sidebar-navigator.php';
require_once __DIR__ . '/../../lib/utils.php';

class NavigatorSection extends RawDataContainer implements Component {
  public function render(): Component {
    $self = $this;
    $currentpage = $this->get('page');
    $subpagetmpls = $this->get('subpages');

    $genretmpls = array_map(
      function (array $info) use($self) {
        [$id, $name] = $info;

        return [
          'href' => $self
            ->get('url-query')
            ->assign(['page' => 'genre', 'genre' => $id])
            ->getUrlQuery(),

          'page' => 'genre',
          'title' => $name,
        ];
      },

      $this->get('genre-manager')->list()
    );

    return new SidebarNavigator(
      [
        ['', $subpagetmpls],
        ['Thể loại', $genretmpls],
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
