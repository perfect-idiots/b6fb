<?php
require_once __DIR__ . '/base.php';

class ToggleFavouriteButton implements Component {
  public function render(): Component {
    return HtmlElement::emmetTop('button.toggle-favourite', [
      'style' => [
        'cursor' => 'pointer',
      ],
    ]);
  }
}
?>
