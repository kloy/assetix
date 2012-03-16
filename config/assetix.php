<?php

return array(
	// Debug mode turns off some filters such as yuicompressor.
	// Set to true for production.
	'debug' => true,
	// Path to yuicompressor
	'yuicompressor_path' => ASSETIX_PATH.'/bin/yuicompressor-2.4.7.jar',
	// Path to nodejs executable
	'node_path' => '/usr/bin/node',
	// Path to use for cache
	'cache_path' => ASSETIX_PATH.'/cache',
);