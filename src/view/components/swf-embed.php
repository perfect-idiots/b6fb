<?php
require_once __DIR__ . '/base.php';

class SwfEmbed implements Component {
  private $src;
  const MIME = 'application/x-shockwave-flash';

  public function __construct(string $src) {
    $this->src = $src;
  }

  public function render(): Component {
    return HtmlElement::create('embed', [
      'src' => $this->src,
      'type' => static::MIME,
    ]);
  }

  static public function id(UrlQuery $urlQuery, string $id): self {
    return new static($urlQuery->assign([
      'purpose' => 'game-swf',
      'type' => 'file',
      'mime' => static::MIME,
      'name' => $id,
    ])->getUrlQuery());
  }
}
?>
