<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/markdown-view.php';

class SwfEmbed implements Component {
  private $src;
  const MIME = 'application/x-shockwave-flash';

  public function __construct(string $src) {
    $this->src = $src;
  }

  public function render(): Component {
    return HtmlElement::emmetBottom('.embed-container>embed.embed', [
      'src' => $this->src,
      'type' => static::MIME,
      new SwfEmbedChild(),
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

class SwfEmbedChild implements Component {
  public function render(): Component {
    return MarkdownView::indented("
      ## Không hỗ trợ

      ### Cách khắc phục

      * Kiểm tra hỗ trợ Flash Player trên trình duyệt của bạn, bật flash plugin nếu có.

      * Một số trình duyệt không có sẵn flash:
        - Cài [Google Chrome](https://www.google.com/chrome/)
        - Cài [Firefox](https://www.mozilla.org/en-US/firefox/)
        - [Cài flash plugin](https://get.adobe.com/flashplayer/)

      ### Thông tin thêm

      **Trình duyệt:**
        * _User Agent:_ <code>{$_SERVER['HTTP_USER_AGENT']}</code>
    ");
  }
}
?>
