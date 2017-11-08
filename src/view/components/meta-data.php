<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class MetaData extends RawDataContainer implements Component {
  public function render(): Component {
    return HtmlElement::create('meta', $this->getData());
  }
}

class CharsetMetaData implements Component {
  private $charset;

  public function __construct(string $charset = 'utf-8') {
    $this->charset = $charset;
  }

  public function render(): Component {
    return new MetaData($this->charset);
  }
}

abstract class ContentMetaData implements Component {
  private $key, $content, $attr;
  abstract protected function field(): string;

  public function __construct(string $key, string $content, array $attr = array()) {
    $this->key = $key;
    $this->content = $content;
  }

  public function render(): Component {
    return new MetaData(array_merge(
      array(
        $this->field() => $this->name,
        'content' => $this->content,
      ),
      $this->attr
    ));
  }
}

class NamedMetaData extends ContentMetaData {
  protected function field(): string {
    return 'name';
  }
}

class HttpEquivMetaData extends ContentMetaData {
  protected function field(): string {
    return 'http-equiv';
  }
}
?>
