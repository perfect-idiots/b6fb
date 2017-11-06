<?php
require_once __DIR__ . '/../model/url-query.php';
$GLOBALS['URL_QUERY'] = new UrlQuery($_GET);
?>
