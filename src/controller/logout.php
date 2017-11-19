<?php
require_once __DIR__ . '/../model/index.php';
require_once __DIR__ . '/../lib/utils.php';

class Logout extends RawDataContainer {
  public function act(): void {
    $prefix = $this->getDefault('prefix', '');

    $toberemoved = [
      $prefix . 'logged-in',
      $prefix . 'username',
      $prefix . 'password',
    ];

    $this->get('cookie')->without($toberemoved)->update();
    $this->get('post-data')->without($toberemoved)->update($_POST);
  }
}
?>
