<?php
require_once __DIR__ . '/../lib/utils.php';
require_once __DIR__ . '/../lib/render.php';
require_once __DIR__ . '/../lib/http-status-table.php';
require_once __DIR__ . '/components/base.php';
require_once __DIR__ . '/components/app.php';
require_once __DIR__ . '/components/admin-user-interface.php';
require_once __DIR__ . '/components/login-user-interface.php';
require_once __DIR__ . '/components/sign-up-user-interface.php';
require_once __DIR__ . '/components/meta-element.php';
require_once __DIR__ . '/components/css-view.php';
require_once __DIR__ . '/components/script-embed.php';

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

class LoginPage extends Page {
  protected function component(): Component {
    return new LoginUserInterface($this->getData());
  }
}

class LogoutPage extends Page {
  protected function component(): Component {
    $this->get('logout')->act();
    $prev = $this->getDefault('previous-page', 'index');
    $next = $this->get('url-query')->set('page', $prev);
    $next->redirect();
    return new RedirectPage($next->getUrlQuery());
  }
}

class SignUpPage extends Page {
  protected function component(): Component {
    return new SignUpUserInterface($this->getData());
  }
}

class AdminPage extends Page {
  protected function component(): Component {
    return new AdminUserInterface($this->getData());
  }
}

class ErrorPage extends Page {
  protected function component(): Component {
    $status = $this->get('status');
    $exception = $this->get('exception');
    $message = HttpStatusTable::create()->get($status);
    http_response_code($status);

    $exceptionMessage = $exception ? $exception->getMessage() : '';

    return HtmlElement::create('html', [
      'lang' => 'en',
      'classes' => ['error', 'message'],
      'dataset' => ['status' => $status],
      HtmlElement::create('head', [
        new CharsetMetaElement('utf-8'),
        new NamedMetaElement('status', $status),
        HtmlElement::create('title', "$status: $message"),
        CssView::fromFile(__DIR__ . '/../resources/styles/error.css', [
          'text-color' => 'black',
          'background-color' => 'white',
          'header-background-color' => 'white',
        ]),
      ]),
      HtmlElement::create('body', [
        'style' => [
          'text-align' => 'center',
          'font-family' => 'sans-serif',
        ],
        HtmlElement::nested(['header', 'h1', 'code'], [
          'style' => [
            'color' => 'red',
            'font-weight' => 'normal',
            'font-size' => '5em',
          ],
          (string) $status,
        ]),
        HtmlElement::nested(['main', 'output'], [
          HtmlElement::create('div', [
            'style' => [
              'background-color' => 'yellow',
              'font-size' => '3em',
              'display' => 'block',
              'height' => '100%',
            ],
            $message,
          ]),
          HtmlElement::create('div', [
            'style' => [
              'font-size' => '1.5em',
            ],
            $exceptionMessage
          ]),
        ]),
        JavascriptEmbed::text(
          "console.error(new Error('HTTP Status: $status — $message'))",
          ['classes' => ['error-logger']]
        ),
      ]),
    ]);
  }

  static public function status(int $status,  ?Exception $exception = null): self {
    return new static([
      'status' => $status,
      'exception' => $exception,
    ]);
  }
}
?>
