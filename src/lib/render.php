<?php
require_once __DIR__ . '/components/base.php';

class Renderer {
  private $production;

  public function __construct(bool $production = false) {
    $this->production = $production;
  }

  public function render(Component $component): string {
    return renderLevel($component, 0);
  }

  private function renderLevel(Component $component, int $level): string {
    if ($component instanceof PrimaryComponent) {
      if ($component instanceof Element) return $this->renderElement($component, $level);
      if ($component instanceof TextBase) return $this->renderText($component);

      throw new TypeError('Cannot render custom PrimaryComponent');
    }

    if ($component instanceof Component) {}

    throw new TypeError('Must be an instance of Component');
  }

  private function renderElement(Element $element, int $level): string {
    $tag = $element->tag;
    $attributes = $this->renderAttributes($element->attributes);
    $classes = $this->renderClassAttribute($element->classes);
    $style = $this->renderStyleAttribute($element->style);
    $data = $this->renderDataAttribute($element->data);

    $newlevel = $level + 1;
    $newline = $this->newline();
    $indent = $this->indent($newlevel);

    $result = "<$tag $attributes $classes $style $data>$newline";

    foreach($element->children as $child) {
      $childHTML = $this->renderLevel($child, $newlevel);
      $result .= $indent . $childHTML;
    }

    return "$result</$tag>$newline";
  }

  private function renderText(TextBase $text): string {
    return $text->getText();
  }

  private function renderAttributes(array $attributes): string {
    $result = array();

    foreach($attributes as $key => $value) {
      $actualKey = htmlspecialchars($key);
      $actualValue = htmlspecialchars($value);

      array_push($result, "$actualKey=\"$actualValue\"");
    }

    return join(' ', $result);
  }

  private function renderClassAttribute(array $classes): string {
    if (!sizeof($classes)) return '';

    return 'class="' . join(
      ' ',
      array_map(
        function ($x) { return htmlspecialchars($x); },
        $classes
      )
    ) . '"';
  }

  private function renderStyleAttribute(array $style) {
    if (!sizeof($style)) return '';

    $result = 'style="';

    foreach($style as $key => $value) {
      $actualKey = htmlspecialchars($key);
      $actualValue = htmlspecialchars(is_array($value) ? join(' ', $value) : $value);

      $result .= "$actualKey: $actualValue; ";
    }

    return $result.'"';
  }

  private function renderDataAttribute(array $data): string {
    if (!sizeof($data)) return '';

    $result = array();

    foreach($data as $key => $value) {
      $result["data-$key"] = $value;
    }

    return $this->renderAttributes($result);
  }

  private function newline(): string {
    return $this->production ? '' : "\n";
  }

  private function indent(int $level): string {
    return $this->production ? '' : str_repeat(' ', $level << 1);
  }
}
?>
