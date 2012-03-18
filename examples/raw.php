<?php

$base_dir = dirname(__FILE__).'/..';

require_once("$base_dir/classes/assetix.php");

use Assetix\Assetix;

// Clear cache for testing purposes
shell_exec("rm $base_dir/cache/*");
shell_exec("rm $base_dir/assets/production/*");

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