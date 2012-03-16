<?php

require_once('classes/assetix.php');

use Assetix\Assetix;

// Instantiate Assetix
$assetix = new Assetix();

// Add css files to group base_css
$css = array('/css/test.css');
$assetix->css('base_css', $css);

// Add js files to group base_js
$js = array('/js/underscore.js', '/js/*');
$assetix->js('base_js', $js);

// Echo out raw compiled files
echo $assetix->css('base_css', true)."\n";
echo $assetix->js('base_js', true)."\n";