<?php

return array(
	// Replacement to use when pattern is matched during css rewrite
	'css_rewrite_replacement' => '\\1/assets/production/',
	// Path to use for asset cache
	'cache_path' => __DIR__.'/../cache/',
	// Absolute path to output assets to
	'output_absolute_path' => __DIR__.'/../assets/production/',
	// Path assets are located at
	'asset_path' => __DIR__.'/../assets/',
	// Debug mode turns off some filters such as yuicompressor.
	// Set to false for production.
	'debug' => true,
	// Current assets version. Update to break production cache.
	'assets_version' => '0.0.2',
);