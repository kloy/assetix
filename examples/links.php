<?php

$base_dir = dirname(__FILE__).'/..';

require $base_dir.'/vendor/.composer/autoload.php';

use Assetix\Assetix;

// Clear cache and production assets for testing purposes. Do not do this unless you wish
// to force recompilation even when an asset has not been changed.
$cache_path = "$base_dir/cache/*";
array_map("unlink", glob($cache_path));
$prod_path = "$base_dir/assets/production/*";
array_map("unlink", glob($prod_path));

// Instantiate Assetix
$assetix = new Assetix(require("$base_dir/config/assetix.php"));

// Add css files to group base_css
$delay = microtime(true);
$css = array('/css/test.css');
$assetix->css('base_css', $css);
$assetix->css('ie_base_css', $css);
$delay = round(microtime(true) - $delay, 4) . " seconds";
echo "CSS delay:\t$delay\n";

// Add less files to group base_less
$delay = microtime(true);
$less = array('/less/test.less');
$assetix->less('base_less', $less);
$assetix->less('ie_base_less', $less);
$delay = round(microtime(true) - $delay, 4) . " seconds";
echo "LESS delay:\t$delay\n";

// Add styl files to group base_styl
$delay = microtime(true);
$styl = array('/styl/test.styl');
$assetix->styl('base_styl', $styl);
$assetix->styl('ie_base_styl', $styl);
$delay = round(microtime(true) - $delay, 4) . " seconds";
echo "Stylus delay:\t$delay\n";

// Add js files to group base_js
$delay = microtime(true);
$js = array('/js/*');
$assetix->js('base_js', $js);
$delay = round(microtime(true) - $delay, 4) . " seconds";
echo "JS delay:\t$delay\n";

// Add coffee-script files to group base_coffee
$delay = microtime(true);
$assetix->coffee('base_coffee', array('/coffee/test.coffee'));
$delay = round(microtime(true) - $delay, 4) . " seconds";
echo "Coffee delay:\t$delay\n";

// Add js files to group base_js
$delay = microtime(true);
$underscore = array('/jst/*.jst');
$assetix->underscore('base_underscore', $underscore);
$delay = round(microtime(true) - $delay, 4) . " seconds";
echo "Underscore delay:\t$delay\n";

// Echo out raw compiled files
// echo $assetix->js('base_js')."\n";
// echo $assetix->coffee('base_coffee')."\n";
// echo $assetix->underscore('base_underscore')."\n";
echo $assetix->css('base_css')."\n";
echo $assetix->css('ie_base_css')."\n";
echo $assetix->less('base_less')."\n";
echo $assetix->less('ie_base_less')."\n";
echo $assetix->styl('base_styl')."\n";
echo $assetix->styl('ie_base_styl')."\n";