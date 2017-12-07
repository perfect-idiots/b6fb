<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/template-set.php';
require_once __DIR__ . '/warning-bar.php';
require_once __DIR__ . '/toggle-favourite-button.php';
require_once __DIR__ . '/../../lib/utils.php';

class MainTemplateSet extends RawDataContainer implements Component {
  public function render(): Component {
    $data = $this->getData();

    return new TemplateSet([
      'warning-bar' => new WarningBar(),
      'toggle-favourite-button' => new ToggleFavouriteButton(),
    ]);
  }
}
?>
