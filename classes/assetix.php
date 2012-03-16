<?php

namespace Assetix;

define('ASSETIX_PATH', __DIR__.'/..');
define('ASSETIX_ASSET_PATH', ASSETIX_PATH.'/assets');

// Composer autoloader
require ASSETIX_PATH.'/classes/compiler.php';

class Assetix
{
	protected $_compiled = null;

	function __construct($config = array())
	{
		if (count($config) === 0)
		{
			$config = require(ASSETIX_PATH.'/config/assetix.php');
		}
		$this->_compiler = new Compiler($config);
	}

	// Get a css asset
	function css($group = '', $files = array(), $raw = false)
	{
		$compiled = $this->_compiler->css($group, $files);

		if ($raw === true)
		{
			return $compiled;
		}
	}

	// Get a js asset
	function js($group = '', $files = array(), $raw = false)
	{
		$compiled = $this->_compiler->js($group, $files);

		if ($raw === true)
		{
			return $compiled;
		}
	}

	function write($contents)
	{
		// $this->_aw->write(BASEPATH.'/production', $contents);
	}
}