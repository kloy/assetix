<?php

require('assetix.php');

$assetix = new Assetix\Assetix();
$assetix->css('base_css', array('/css/test.css'));

echo $assetix->css('base_css')."\n";