<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/component-base.php';

class Renderer {
  private $production;

  public function __construct(bool $production = false) {
    $this->production = $production;
  }

  public function render(Component $component): string {
    return $this->renderLevel($component, 0, []);
  }

  private function renderLevel(Component $component, int $level, array $compClassNames): string {
    if ($component instanceof PrimaryComponent) {
      if ($component instanceof Element) return $this->renderElement($component, $level, $compClassNames);
      if ($component instanceof TextBase) return $this->renderText($component, $level);

      throw new TypeError('Cannot render custom PrimaryComponent');
    }

    if ($component instanceof Component) {
      $nextCompClassNames = array_merge($compClassNames, [get_class($component)]);
      return $this->renderLevel($component->render(), $level, $nextCompClassNames);
    }

    throw new TypeError('Must be an instance of Component');
  }

  private function renderElement(Element $element, int $level, array $compClassNames): string {
    $tag = $element->tag;
    $classmap = Renderer::makeComponentClassMap($compClassNames);

    $attributes = $this->renderAttributes(array_merge(
      $element->attributes,
      ['x-component-level' => (string) $level],
      ['x-component' => implode(' ', $classmap['set'])]
    ));

    $classes = $this->renderClassAttribute(array_merge(
      $element->classes,
      ["x-component-level--$level"],

      array_map(
        function (string $name): string {
          $kebab = CaseConverter::fromPascalCase($name)->toKebabCase();
          return 'x-component--' . $kebab;
        },

        $classmap['set']
      )
    ));

    $style = $this->renderStyleAttribute($element->style);

    $data = $this->renderDatasetAttribute($element->dataset);

    $newlevel = $level + 1;
    $newline = $this->newline();
    $indent = $this->indent($level);

    $open = Renderer::joinStringSegments(
      [$tag, $attributes, $classes, $style, $data]
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
      $childHTML = $this->renderLevel($child, $newlevel, []);
      $result .= $childHTML . $newline;
    }

    return "{$result}{$indent}</$tag>";
  }

  private function renderText(TextBase $text, int $level): string {
    $segments = explode("\n", $text->getText());
    $indent = $this->indent($level);
    $result = [];

    foreach($segments as $chunk) {
      array_push($result, $indent . $chunk);
    }

    return implode("\n", $result);
  }

  private function renderAttributes(array $attributes): string {
    $result = [];

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

    $result = [];

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

  static private function makeComponentClassMap(array $list): array {
    $set = [];
    $tree = [];

    foreach($list as $component) {
      $checker = new ClassChecker($component);
      if (!$checker->didImplemented('Component')) continue;
      if ($checker->didExtended('PrimaryComponent')) continue;

      $unit = array_merge(
        [$component => $component],
        array_filter(
          $checker->getParents(),
          function (string $class): bool {
            return in_array('Component', class_implements($class));
          }
        )
      );

      $set = array_merge($set, $unit);
      array_push($tree, array_values($unit));
    }

    return [
      'set' => array_unique(array_values($set)),
      'tree' => $tree,
    ];
  }
}
?>
