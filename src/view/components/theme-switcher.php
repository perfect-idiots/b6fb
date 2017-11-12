<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class ThemeSwitcher extends RawDataContainer implements Component {
  public function render(): Component {
    $urlQuery = $this->get('url-query');
    $currentTheme = $this->get('theme-name');
    $futureTheme = self::reverseTheme($currentTheme);

    return HtmlElement::nested(['button', 'a'], [
      'href' => $urlQuery->set('theme', $futureTheme)->getUrlQuery(),
      'dataset' => [
        'current-theme' => $currentTheme,
        'future-theme' => $futureTheme,
      ],
      'Switch Theme',
    ]);
  }

  static private function reverseTheme(string $theme): string {
    switch ($theme) {
      case 'dark':
        return 'light';
      case 'light':
        return 'dark';
      default:
        throw new Exception("Invalid theme name: $theme");
    }
  }
}
?>
