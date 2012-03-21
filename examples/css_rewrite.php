<?php

// Example shows how to use the css rewrite filter

$base_dir = dirname(__FILE__);

require $base_dir.'/../vendor/.composer/autoload.php';

use Assetix\Assetix;

// Instantiate Assetix
$assetix = new Assetix(require("config/assetix.php"));

// Clear cache and production assets for testing purposes. Do not do this unless you wish
// to force recompilation even when an asset has not been changed.
$assetix->clear_cache();
$assetix->clear_production();

$test1 = $assetix->css('ie_base_css', array('/rewrite_css/test.1.css'), true);
$assetix->set_rewrite('/bar');
$test2 = $assetix->css('ie_base_css', array('/rewrite_css/test.2.css'), true);
$assetix->set_rewrite('/man');
$test3 = $assetix->css('ie_base_css', array('/rewrite_css/test.3.css'), true);

echo "### TEST 1 ###\n$test1\n";
echo "### TEST 2 ###\n$test2\n";
echo "### TEST 3 ###\n$test3\n";