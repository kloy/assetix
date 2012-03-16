<?php

require_once('classes/assetix.php');

use Assetix\Assetix;

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

// Echo out raw compiled files
echo $assetix->js('base_js', true)."\n";
echo $assetix->css('base_css', true)."\n";
echo $assetix->less('base_less', true)."\n";