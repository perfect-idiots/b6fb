<?php
$verstr = phpversion();
$verarr = explode('.', $verstr);

if ($verarr[0] < 7 || $verarr[1] < 1) {
  http_response_code(500);

  die("
    <body>
      <h1>Error</h1>
      <p>
        Requires <b>PHP v7.1.0</b> or later
      </p>
      <p>
        Current version is <i>$verstr</i>
      </p>
      <p>
        Please upgrade your
        <a href='http://php.net/downloads.php'>PHP</a>
        immediately!
      </p>
      <script type='text/javascript'>
        throw new Error('Server does not work because its owner still lives in Stone Age.');
      </script>
    </body>
  ");
}
?>
