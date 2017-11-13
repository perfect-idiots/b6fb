<?php
interface Component {
  public function render(): Component;
}

class PrimaryComponent implements Component {
  public function render(): Component {
    throw new TypeError('Cannot render a primary component.');
  }
}

abstract class Element extends PrimaryComponent {
  private const SPECIAL_FIELDS = [
    'attributes', 'classes', 'style', 'dataset', 'children'
  ];

  public $tag, $attributes, $children, $classes, $style, $dataset;

  public function __construct(string $tag, array $props = [], array $children = []) {
    $this->tag = $tag;
    $this->children = $children;

    $this->attributes = Element::getArrayKey($props, 'attributes');
    $this->classes = Element::getArrayKey($props, 'classes');
    $this->style = Element::getArrayKey($props, 'style');
    $this->dataset = Element::getArrayKey($props, 'dataset');
  }

  abstract public function tagClosingStyle(): string;

  static public function create(string $tag, $desc = []): self {
    if (gettype($desc) != 'array') return static::create($tag, [$desc]);

    $attributes = Element::getArrayKey($desc, 'attributes');
    $classes = Element::getArrayKey($desc, 'classes');
    $style = Element::getArrayKey($desc, 'style');
    $dataset = Element::getArrayKey($desc, 'dataset');
    $children = Element::getArrayKey($desc, 'children');

    foreach ($desc as $key => $value) {
      if (is_long($key)) {
        array_push($children, $value);
      } else if (!in_array($key, Element::SPECIAL_FIELDS)) {
        if ($value === false) continue;
        $attributes[$key] = $value === true ? $key : $value;
      }
    }

    return new static(
      $tag,
      [
        'attributes' => $attributes,
        'classes' => $classes,
        'style' => $style,
        'dataset' => $dataset
      ],
      array_map(
        function ($x) {
          return $x instanceof Component ? $x : new TextNode((string) $x);
        },
        $children
      )
    );
  }

  static public function nested(array $tags, $desc = []) {
    return sizeof($tags)
      ? static::create(
        $tags[0],
        static::nested(array_slice($tags, 1), $desc)
      )
      : $desc
    ;
  }

  static public function emmet(string $abbr, callable $fn = null): EmmetConstructTree {
    return EmmetConstructTree::instance(get_called_class(), $abbr, $fn);
  }

  static public function emmetFromArray(string $abbr, array $attrTable): EmmetConstructTree {
    $getAttrIf = function (bool $condition, string $key) use($attrTable) {
      if (!$condition) return [];
      if (!array_key_exists($key, $attrTable)) return [];
      return $attrTable[$key];
    };

    $callback = function ($params) use($getAttrIf) {
      [
        'at-top' => $atTop,
        'at-bottom' => $atBottom,
        'deep' => $deep,
      ] = $params;

      return array_merge(
        $getAttrIf($atTop, 'at-top'),
        $getAttrIf($atBottom, 'at-bottom'),
        $getAttrIf($atTop && $atBottom, 'no-children'),
        $getAttrIf(!$atTop && !$atBottom, 'in-middle'),
        $getAttrIf($atTop != $atBottom, 'at-both-ends')
      );
    };

    return static::emmet($abbr, $callback);
  }

  static private function getArrayKey(array $array, string $key): array {
    return array_key_exists($key, $array) && $array[$key] ? $array[$key] : [];
  }
}

class XmlElement extends Element {
  public function tagClosingStyle(): string {
    return sizeof($this->element) ? 'non-empty' : 'self-close';
  }
}

class HtmlElement extends Element {
  private const EMPTY_TAGS = [
    'area', 'base', 'br', 'col', 'embed',
    'hr', 'img', 'input', 'keygen', 'link',
    'meta', 'param', 'source', 'track', 'wbr'
  ];

  public function tagClosingStyle(): string {
    if (in_array($this->tag, HtmlElement::EMPTY_TAGS)) return 'self-close';
    if (sizeof($this->children)) return 'non-empty';
    return 'pair-close';
  }
}

abstract class TextBase extends PrimaryComponent {
  abstract public function getText(): string;
}

class TextNode extends TextBase {
  private $text;

  public function __construct(string $text) {
    $this->text = $text;
  }

  public function getText(): string {
    return htmlspecialchars($this->text);
  }
}

class UnescapedText extends TextBase {
  private $text;

  public function __construct(string $text) {
    $this->text = $text;
  }

  public function getText(): string {
    return $this->text;
  }
}

abstract class EmmetConstruct implements Component {
  abstract public function build(): Component;
  abstract static protected function sharedCreate(array $params): EmmetConstruct;

  public function render(): Component {
    return $this->build();
  }
}

class EmmetConstructNode extends EmmetConstruct {
  private $constructed;

  private function __construct(Component $constructed) {
    $this->constructed = $constructed;
  }

  static protected function sharedCreate(array $params): EmmetConstruct {
    return new static($params['constructed']);
  }

  public function build(): Component {
    return $this->constructed;
  }
}

class EmmetConstructTree extends EmmetConstruct {
  private $elementClass, $abbr, $fn;

  public function __construct(string $elementClass, string $abbr, callable $fn) {
    $this->elementClass = $elementClass;
    $this->abbr = $abbr;
    $this->fn = $fn;
  }

  static public function instance(string $elementClass, string $abbr, $fn): self {
    if (!$fn) $fn = [get_called_class(), 'getEmptyArray'];
    if (!is_callable($fn)) throw new TypeError('Callback must be callable');
    return new static($elementClass, $abbr, $fn);
  }

  static private function getEmptyArray(): array {
    return [];
  }

  public function build(): Component {
    return self::nested($this->abbr, $this->fn, [
      'deep' => 0,
      'at-top' => true,
      'emmet-object' => $this,
      'emmet-class' => get_called_class(),
      'node-attributes' => [],
    ]);
  }

  static protected function sharedCreate(array $params): EmmetConstruct {
    return static::instance(
      $params['element-class'],
      $params['abbr'],
      $params['callback']
    );
  }

  static private function nested(string $abbr, callable $fn, array $params): EmmetConstruct {
    $nested = explode('>', $abbr, 3);
    $nestedCount = sizeof($nested);
    $prefix = $nested[0];
    $midfix = $nestedCount > 1 ? $nested[1] : '';
    $suffix = $nestedCount > 2 ? $nested[2] : '';
    ['deep' => $deep] = $params;

    if (!$midfix) {
      return self::unnested($prefix, $fn, array_merge($params, [
        'at-bottom' => true,
      ]));
    }

    $nodeAttributes = $fn(array_merge($params, [
      'at-bottom' => false,
    ]));

    $nestedfn = function (array $params) use($midfix, $suffix, $fn) {
      $newParams = array_merge($params, [
        'node-attributes' => [],
      ]);
      return $params['emmet-class']::sibling($midfix, $suffix, $fn, $newParams);
    };

    return self::unnested($prefix, $nestedfn, array_merge($params, [
      'node-attributes' => $nodeAttributes,
    ]));
  }

  static private function sibling(string $abbr, string $suffix, callable $fn, array $params): array {
    $children = [];
    $childid = 0;
    $abbrAddend = $suffix ? ">$suffix" : '';

    $commonParams = array_merge($params, [
      'at-top' => false,
      'next-abbr' => $suffix,
      'deep' => $params['deep'] + 1,
    ]);

    foreach (explode('+', $abbr) as $siblingid => $sibling) {
      $siblingParams = array_merge($commonParams, [
        'child-abbr' => $abbr,
        'sibling-id' => $siblingid,
        'sibling-abbr' => $sibling,
      ]);

      if (preg_match('/\*/', $sibling)) {
        [$repeated, $factor] = explode('*', $sibling);

        if (!preg_match('/^[0-9]+$/', $factor)) {
          throw new TypeError("Factor is not a number: '$factor'");
        }

        $factor = (int) $factor;

        foreach (range(0, $factor - 1) as $repeatedid) {
          $nextAbbr = $repeated . $abbrAddend;

          array_push($children, self::nested($nextAbbr, $fn, array_merge($siblingParams, [
            'position' => 'repeated',
            'child-id' => $childid,
            'repeated-id' => $repeatedid,
            'repeated-abbr' => $repeated,
            'repeated-factor' => $factor,
            'nested-abbr' => $nextAbbr,
          ])));

          $childid += 1;
        }
      } else {
        $nextAbbr = $sibling . $abbrAddend;

        array_push($children, self::nested($nextAbbr, $fn, array_merge($siblingParams, [
          'position' => 'sibling',
          'child-id' => $childid,
          'nested-abbr' => $nextAbbr,
        ])));

        $childid += 1;
      }
    }

    return $children;
  }

  static private function unnested(string $abbr, callable $fn, array $params): EmmetConstruct {
    preg_match('/(^[^.#]*)/', $abbr, $taglist);
    $tag = $taglist[0] ? $taglist[0] : $params['emmet-object']->defaultTagName();
    self::validateTagAttrName($tag, 'tag name');

    preg_match_all('/#([^.#]+)/', $abbr, $idmatches);
    $idlist = $idmatches[1];
    if (sizeof($idlist) > 1) throw new Error("Cannot specify more than 1 id: '$abbr'");
    $id = sizeof($idlist) ? $idlist[0] : '';
    $idAttr = $id ? ['id' => $id] : [];

    preg_match_all('/\.([^.#]+)/', $abbr, $classmatches);
    $classes = $classmatches[1];
    $classesAttr = sizeof($classes)
      ? ['classes' => $classes]
      : []
    ;

    $extendedAttr = $fn(array_merge($params, [
      'abbr' => $abbr,
      'id' => $id,
      'id-attr' => $idAttr,
      'classes' => $classes,
      'classes-attr' => $classesAttr,
    ]));

    $elementClass = $params['emmet-object']->elementClassName();
    $constructedComponent = $elementClass::create($tag, array_merge(
      $idAttr,
      $classesAttr,
      $params['node-attributes'],
      $extendedAttr
    ));
    return EmmetConstructNode::sharedCreate(['constructed' => $constructedComponent]);
  }

  public function defaultTagName(): string {
    return 'div';
  }

  public function elementClassName(): string {
    return $this->elementClass;
  }

  static private function validateTagAttrName(string $name, string $role): void {
    if (!preg_match('/^[a-z]([a-z0-9\-:]*[a-z0-9])?$/', $name)) {
      throw new Error("'$name' is not a valid $role");
    }
  }
}
?>
