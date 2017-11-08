<?php
require_once __DIR__ . '/base.php';

class CssView implements Component {
  private $css, $variables;

  public function __construct(string $css, array $variables = array()) {
    $this->css = $css;
    $this->variables = $variables;
  }

  static public function fromFile(string $filename, array $variables = array()): self {
    return new static(file_get_contents($filename), $variables);
  }

  public function render(): Component {
    $css = trim($this->css);

    foreach($this->variables as $key => $value) {
      $css = implode(
        (string) $value,
        explode("[[$key]]", $css)
      );
    }

    $regres = preg_match('/\[\[[a-zA-Z\-]+\]\]/', $css, $matches);
    if ($regres === false) {
      throw new Exception('An error occurred when counting remain CSS unsupplied variables');
    } else if ($regres !== 0) {
      $list = implode(', ', $matches);
      throw new Error("Some variables are not supplied: $list");
    }

    return HtmlElement::create('style', array(
      'type' => 'text/css',
      new UnescapedText($css),
    ));
  }
}
?>
