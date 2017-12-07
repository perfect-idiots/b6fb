<?php
require_once __DIR__ . '/base.php';
require_once __DIR__ . '/../../lib/utils.php';

class TemplateSet extends RawDataContainer implements Component {
  public function render(): Component {
    $list = $this->getData();

    $children = array_map(
      function (Component $template, string $id) {
        $compClassName = get_class($template);

        return HtmlElement::create('template', [
          'id' => $id,
          'classes' => [
            'item',
            "x-template--$id",
          ],
          'x-template-component' => $compClassName,
          $template,
        ]);
      },
      array_values($list),
      array_keys($list)
    );

    return HtmlElement::create('template-set', array_merge($children, [
      'hidden' => true,
    ]));
  }
}
?>
