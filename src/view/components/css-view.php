<?php
require_once __DIR__ . '/base.php';

class CssView implements Component {
  private $css, $variables;

  public function __construct(string $css, array $variables = array()) {
    $this->css = $css;
    $this->variables = $variables;
  }

  static public function fromFile(string $filename, array $variables = array()): self {
    return new self(file_get_contents($filename), $variables);
  }

  public function render(): Component {
    $css = $this->css;

    foreach($this->variables as $key => $value) {
      $css = implode(
        (string) $value,
        explode("[[$key]]", $css)
      );
    }

    return HtmlElement::create('style', array(
      'type' => 'text/css',
      new UnescapedText($css),
    ));
  }
}
?>
