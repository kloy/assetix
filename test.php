<?php

require_once('classes/assetix.php');

use Assetix\Assetix;

// Clear cache for testing purposes
`rm cache/*`;
`rm assets/production/*`;

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
// echo $assetix->css('base_css', true)."\n";
// echo $assetix->less('base_less', true)."\n";
// echo $assetix->underscore('base_underscore', true)."\n";
echo $assetix->css('base_css')."\n";
echo $assetix->less('base_less')."\n";
echo $assetix->js('base_js')."\n";