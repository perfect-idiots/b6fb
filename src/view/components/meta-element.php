<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class MetaElement extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('meta', $this->getData());
  }
}

class CharsetMetaElement implements Component {
  private $charset, $attr;

  public function __construct(string $charset = 'utf-8', array $attr = []) {
    $this->charset = $charset;
    $this->attr = $attr;
  }

  public function render(): Component {
    return new MetaElement(array_merge(
      ['charset' => 'utf-8'],
      $this->attr
    ));
  }
}

abstract class ContentMetaElement implements Component {
  private $key, $content, $attr;
  abstract protected function field(): string;

  public function __construct(string $key, string $content, array $attr = []) {
    $this->key = $key;
    $this->content = $content;
    $this->attr = $attr;
  }

  public function render(): Component {
    return new MetaElement(array_merge(
      [
        $this->field() => $this->key,
        'content' => $this->content,
      ],
      $this->attr
    ));
  }
}

class NamedMetaElement extends ContentMetaElement {
  protected function field(): string {
    return 'name';
  }
}

class HttpEquivMetaElement extends ContentMetaElement {
  protected function field(): string {
    return 'http-equiv';
  }
}
?>
