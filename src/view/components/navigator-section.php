<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/sidebar-navigator.php';
require_once __DIR__ . '/../../lib/utils.php';

class NavigatorSection extends RawDataContainer implements Component {
  public function render(): Component {
    $self = $this;
    $currentpage = $this->get('page');
    $currentgenre = $this->get('url-query')->getDefault('genre', '');
    $subpagetmpls = $this->get('subpages');

    $genretmpls = array_map(
      function (array $info) {
        [$id, $name] = $info;

        return [
          'href' => UrlQuery::instance([
            'page' => 'genre',
            'genre' => $id,
          ])->getUrlQuery(),

          'page' => 'genre',
          'genre' => $id,
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
      $currentpage === 'genre'
        ? function ($tmpl) use($currentgenre) {
          return array_key_exists('genre', $tmpl) && $tmpl['genre'] === $currentgenre;
        }
        : function ($tmpl) use($currentpage) {
          return $tmpl['page'] === $currentpage;
        }
    );
  }
}
?>
