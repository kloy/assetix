<?php

$base_dir = dirname(__FILE__).'/..';

require_once("$base_dir/classes/assetix.php");

use Assetix\Assetix;

// Clear cache and production assets for testing purposes. Do not do this unless you wish
// to force recompilation even when an asset has not been changed.
$cache_path = "$base_dir/cache/*";
array_map("unlink", glob($cache_path));
$prod_path = "$base_dir/assets/production/*";
array_map("unlink", glob($prod_path));

// Instantiate Assetix
$assetix = new Assetix();

// Add css files to group base_css
$css = array('/css/test.css');
$assetix->css('base_css', $css);

// Add less files to group base_less
$less = array('/less/test.less');
$assetix->less('base_less', $less);

// Add js files to group base_js
$js = array('/js/underscore.js', '/js/*');
$assetix->js('base_js', $js);

// Add js files to group base_js
$underscore = array('/jst/*.jst');
$assetix->underscore('base_underscore', $underscore);

// Echo out raw compiled files
// echo $assetix->js('base_js', true)."\n";
echo $assetix->css('base_css', true)."\n";
echo $assetix->less('base_less', true)."\n";
echo $assetix->underscore('base_underscore', true)."\n";