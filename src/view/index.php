<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/http-status-table.php';
require_once __DIR__ . '/components/base.php';
require_once __DIR__ . '/components/app.php';
require_once __DIR__ . '/components/meta-element.php';

abstract class Page extends RawDataContainer {
  abstract protected function component(): Component;

  public function render(): string {
    $renderer = new Renderer(false);
    $component = $this->component();
    $html = $renderer->render($component);
    return "<!DOCTYPE html>\n$html\n";
  }
}

class MainPage extends Page {
  protected function component(): Component {
    return new App($this->getData());
  }
}

class ErrorPage extends Page {
  protected function component(): Component {
    $status = $this->get('status');
    $message = (new HttpStatusTable())->get($status);
    http_response_code($status);

    return HtmlElement::create('html', array(
      'lang' => 'en',
      HtmlElement::create('head', array(
        new CharsetMetaElement('utf-8'),
        new NamedMetaElement('status', $status),
        HtmlElement::create('title', "$status: $message"),
      )),
      HtmlElement::create('body', array(
        HtmlElement::nested(array('header', 'h1', 'code'), array(
          (string) $status
        )),
        HtmlElement::nested(array('main', 'output'), $message),
      )),
    ));
  }

  static public function status(int $status): self {
    return new static(array('status' => $status));
  }
}
?>
