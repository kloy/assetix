<?php

return array(
	// Debug mode turns off some filters such as yuicompressor.
	// Set to false for production.
	'debug' => true,
	// Path to yuicompressor
	'yuicompressor_path' => ASSETIX_PATH.'/bin/yuicompressor-2.4.7.jar',
	// Path to nodejs executable
	'node_path' => '/usr/bin/node',
	// Paths used in require by node
	'node_paths' => array(ASSETIX_PATH.'/node_modules'),
	'coffee_path' => ASSETIX_PATH.'/node_modules/.bin/coffee',
	// Path to use for asset cache
	'cache_path' => ASSETIX_PATH.'/cache',
	// Path to cssembed jar
	'cssembed_path' => ASSETIX_PATH.'/bin/cssembed-0.4.5.jar',
	'cssembed_root' => false,
	// Javascript namespace to compile templates under
	'underscore_namespace' => 'JST',
	// Extension for underscore files
	'underscore_ext' => '.jst',
	// Absolute path to output assets to
	'output_absolute_path' => ASSETIX_PATH.'/assets/production',
	// Web path used for serving links
	'output_path' => '/assets/production',
	// Current assets version. Update to break production cache.
	'assets_version' => '0.0.1',
	'asset_path' => ASSETIX_PATH.'/assets',
);