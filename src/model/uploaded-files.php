<?php
require_once __DIR__ . '/../lib/utils.php';

class UploadedFileSet extends LazyLoadedDataContainer {
  protected function load(): array {
    return $_FILES;
  }

  public function getFile(string $name): UploadedFile {
    return new UploadedFile($this->get($name));
  }
}

class UploadedFile extends RawDataContainer {
  public function name(): string {
    return $this->get('name');
  }

  public function basename(): string {
    return basename($this->name());
  }

  public function mimetype(): string {
    return $this->getDefault('type', '');
  }

  public function move(string $destination): bool {
    return move_uploaded_file($this->clientPath(), $destination);
  }
}
?>
