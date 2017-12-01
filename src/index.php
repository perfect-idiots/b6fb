<?php
try {
  require_once __DIR__ . '/controller/index.php';
  echo main();
} catch (Throwable $throwable) {
  echo "<code><pre>$throwable</pre></code>";
}
?>
