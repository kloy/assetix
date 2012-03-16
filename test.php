<?php

require_once('classes/assetix.php');

use Assetix\Assetix;

$assetix = new Assetix();
$css = array('/css/test.css');
$assetix->css('base_css', $css);
$js = array('/js/underscore.js', '/js/*');
$assetix->js('base_js', $js);

echo $assetix->css('base_css', array(), true)."\n";
echo $assetix->js('base_js', array(), true)."\n";