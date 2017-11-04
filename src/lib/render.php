<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/../view/components/base.php';

class Renderer {
  private $production;

  public function __construct(bool $production = false) {
    $this->production = $production;
  }

  public function render(Component $component): string {
    return $this->renderLevel($component, 0, array());
  }

  private function renderLevel(Component $component, int $level, array $prevComponents): string {
    $components = array_merge($prevComponents, array(get_class($component)));

    if ($component instanceof PrimaryComponent) {
      if ($component instanceof Element) return $this->renderElement($component, $level, $components);
      if ($component instanceof TextBase) return $this->renderText($component, $level);

      throw new TypeError('Cannot render custom PrimaryComponent');
    }

    if ($component instanceof Component) {
      return $this->renderLevel($component->render(), $level, $components);
    }

    throw new TypeError('Must be an instance of Component');
  }

  private function renderElement(Element $element, int $level, array $components): string {
    $tag = $element->tag;

    $attributes = $this->renderAttributes(array_merge(
      $element->attributes,
      array('x-component-level' => (string) $level),
      array('x-component' => implode(' ', $components))
    ));

    $classes = $this->renderClassAttribute(array_merge(
      $element->classes,
      array("x-component-level--$level"),

      array_map(
        function (string $name): string {
          $kebab = CaseConverter::fromPascalCase($name)->toKebabCase();
          return 'x-component--' . $kebab;
        },

        $components
      )
    ));

    $style = $this->renderStyleAttribute($element->style);

    $data = $this->renderDatasetAttribute($element->dataset);

    $newlevel = $level + 1;
    $newline = $this->newline();
    $indent = $this->indent($level);

    $open = Renderer::joinStringSegments(
      array($tag, $attributes, $classes, $style, $data)
    );

    switch($element->tagClosingStyle()) {
      case 'self-close':
        return "$indent<$open />";
      case 'pair-close':
        return "$indent<$open></$tag>";
      case 'non-empty':
        break;
      default:
        throw new Exception('Invalid tag closing style');
    }

    $result = "$indent<$open>$newline";

    foreach($element->children as $child) {
      $childHTML = $this->renderLevel($child, $newlevel, array());
      $result .= $childHTML . $newline;
    }

    return "{$result}{$indent}</$tag>";
  }

  private function renderText(TextBase $text, int $level): string {
    $segments = explode("\n", $text->getText());
    $indent = $this->indent($level);
    $result = array();

    foreach($segments as $chunk) {
      array_push($result, $indent . $chunk);
    }

    return implode("\n", $result);
  }

  private function renderAttributes(array $attributes): string {
    $result = array();

    foreach($attributes as $key => $value) {
      $actualKey = htmlspecialchars($key);
      $actualValue = htmlspecialchars($value);

      array_push($result, "$actualKey=\"$actualValue\"");
    }

    return implode(' ', $result);
  }

  private function renderClassAttribute(array $classes): string {
    if (!sizeof($classes)) return '';

    return 'class="' . implode(
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
      $actualValue = htmlspecialchars(is_array($value) ? implode(' ', $value) : $value);

      $result .= "$actualKey: $actualValue; ";
    }

    return $result.'"';
  }

  private function renderDatasetAttribute(array $dataset): string {
    if (!sizeof($dataset)) return '';

    $result = array();

    foreach($dataset as $key => $value) {
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

  static private function joinStringSegments(array $segments) {
    return implode(
      ' ',
      array_filter(
        $segments,
        function ($x) {
          return $x != null && $x !== '';
        }
      )
    );
  }
}
?>
