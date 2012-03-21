<?php

$base_dir = dirname(__FILE__);

require $base_dir.'/../vendor/.composer/autoload.php';

use Assetix\Assetix;

// Instantiate Assetix
$assetix = new Assetix(require("config/assetix.php"));

// Clear cache and production assets for testing purposes. Do not do this unless you wish
// to force recompilation even when an asset has not been changed.
$assetix->clear_cache();
$assetix->clear_production();

// Add css files to group base_css
$css = array('/css/test.css');
$assetix->css('base_css', $css);

// Add less files to group base_less
$less = array('/less/test.less');
$assetix->less('base_less', $less);

// Add js files to group base_js
$js = array('/js/*');
$assetix->js('base_js', $js);

// Add js files to group base_js
$underscore = array('/jst/*.jst');
$assetix->underscore('base_underscore', $underscore);

// Add coffee-script files to group base_coffee
$assetix->coffee('base_coffee', array('/coffee/test.coffee'));

// Add styl files to group base_styl
$styl = array('/styl/test.styl');
$assetix->styl('base_styl', $styl);

// Echo out raw compiled files
echo $assetix->js('base_js', true)."\n";
echo $assetix->css('base_css', true)."\n";
echo $assetix->less('base_less', true)."\n";
echo $assetix->underscore('base_underscore', true)."\n";
echo $assetix->coffee('base_coffee', true)."\n";
echo $assetix->styl('base_styl', true)."\n";