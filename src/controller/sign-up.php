<?php
require_once __DIR__ . '/../lib/utils.php';

class SignUp extends RawDataContainer {
  public function verify(): SignUpInfo {
    return new SignUpInfo();
  }
}

class SignUpInfo extends RawDataContainer {
  public function succeed(): bool {
    return $this->getDefault('succeed', false);
  }

  public function error(): string {
    return $this->getDefault('error', '');
  }

  public function login(): ?LoginInfo {
    return $this->getDefault('login', null);
  }
}
?>
