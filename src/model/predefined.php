<?php
require_once __DIR__ . '/yaml.php';
$GLOBALS['PREDEFINED_GAMES'] = spyc_load_file(__DIR__ . '/predefined/games.yaml');
$GLOBALS['PREDEFINED_GENRES'] = spyc_load_file(__DIR__ . '/predefined/genres.yaml');
?>
