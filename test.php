<?php

require('assetix.php');

$assetix = new Assetix\Assetix();
$css = array('/css/test.css');
$assetix->css('base_css', $css);
$js = array('/js/underscore.js', '/js/*');
$assetix->js('base_js', $js);

echo $assetix->css('base_css')."\n";
echo $assetix->js('base_js')."\n";