<?php

namespace Assetix;

define('ASSETIX_PATH', __DIR__.'/..');
define('ASSETIX_ASSET_PATH', ASSETIX_PATH.'/assets');

// Composer autoloader
require ASSETIX_PATH.'/classes/compiler.php';

class Assetix
{
	function __construct($config = array())
	{
		if (count($config) === 0)
		{
			$config = require(ASSETIX_PATH.'/config/assetix.php');
		}
		$this->_compiler = new Compiler($config);
	}

	function _get_asset_args()
	{
		$arg_count = func_num_args();
		$args = func_get_args();
		if ($arg_count === 1)
		{
			$files = array();
			$raw = false;
		}
		else if ($arg_count === 2)
		{
			if (is_bool($args[1]))
			{
				$files = array();
				$raw = $args[1];
			}
			else if (is_array($args[1]))
			{
				$files = $args[1];
				$raw = false;
			}
			else
			{
				throw new \Exception("Argument 2 must be an array of files or a bool.");
			}
		}
		else
		{
			$files = $args[1];
			$raw = $args[2];
		}

		return array($files, $raw);
	}

	// Get a css asset
	function css($group = '')
	{
		$args = func_get_args();
		list($files, $raw) = call_user_func_array(array($this, "_get_asset_args"), $args);

		$compiled = $this->_compiler->css($group, $files);

		return $this->_render($compiled, $raw);
	}

	// Get a js asset
	function js($group = '')
	{
		$args = func_get_args();
		list($files, $raw) = call_user_func_array(array($this, "_get_asset_args"), $args);

		$compiled = $this->_compiler->js($group, $files);

		return $this->_render($compiled, $raw);
	}

	// Get a js asset
	function less($group = '')
	{
		$args = func_get_args();
		list($files, $raw) = call_user_func_array(array($this, "_get_asset_args"), $args);

		$compiled = $this->_compiler->less($group, $files);

		return $this->_render($compiled, $raw);
	}

	protected function _render($contents, $raw = false)
	{
		if ($raw === true)
		{
			return $contents;
		}
	}

	function write($contents)
	{
		// $this->_aw->write(BASEPATH.'/production', $contents);
	}
}