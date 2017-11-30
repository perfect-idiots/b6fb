<?php
require_once __DIR__ . '/utils.php';

class Constants extends LazyLoadedDataContainer {
  protected function load(): array {
    return self::time();
  }

  private function time(): array {
    $sec = 1000;
    $min = 60 * $sec;
    $hour = 60 * $min;
    $day = 24 * $hour;
    $month = 30 * $day;
    $year = 365 * $month;

    return [
      'second' => $sec,
      'minute' => $min,
      'hour' => $hour,
      'day' => $day,
      'month' => $month,
      'year' => $year,
    ];
  }
}
?>
