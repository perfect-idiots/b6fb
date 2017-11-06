<?php
require_once __DIR__ . '/../lib/yaml.php';
$GLOBALS['LIGHT_THEME_COLORS'] = spyc_load_file(__DIR__ . '/predefined/light.yaml');
$GLOBALS['DARK_THEME_COLORS'] = spyc_load_file(__DIR__ . '/predefined/dark.yaml');
?>
