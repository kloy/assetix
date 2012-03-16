<?php

namespace Assetix;

define('BASEPATH', __DIR__.'/..');
define('ASSET_PATH', BASEPATH.'/assets');

// Composer autoloader
require BASEPATH.'/classes/compiler.php';

class Assetix
{
	protected $_compiled = null;

	function __construct($config = array())
	{
		if (count($config) === 0)
		{
			$config = require(BASEPATH.'/config/assetix.php');
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