<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/parsedown.php';

class MarkdownView implements Component {
  private $html;

  public function __construct(Parsedown $parsedown, string $markdown) {
    $this->html = $parsedown->text($markdown);
  }

  public static function instance(string $markdown): self {
    return new static(Parsedown::instance(), $markdown);
  }

  public static function multiline(array $array, string $separator = "\n"): self {
    return static::instance(implode($separator, $array));
  }

  public static function indented(string $markdown, string $separator = "\n"): self {
    $leastIndent = INF;
    $lines = explode($separator, $markdown);

    foreach ($lines as $line) {
      if (!$line) continue;
      preg_match('/^[ \t]*/', $line, $matches);
      $leastIndent = min($leastIndent, strlen($matches[0]));
    }

    return static::multiline(
      array_map(
        function (string $line) use($leastIndent) {
          return $line ? substr($line, $leastIndent) : '';
        },
        $lines
      ),
      $separator
    );
  }

  public function render(): Component {
    return new UnescapedText($this->html);
  }
}
?>
