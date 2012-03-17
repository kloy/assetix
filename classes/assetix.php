<?php

namespace Assetix;

define('ASSETIX_PATH', __DIR__.'/..');
define('ASSETIX_ASSET_PATH', ASSETIX_PATH.'/assets');

// Composer autoloader
require ASSETIX_PATH.'/classes/compiler.php';

class Assetix
{
	protected $_config = array();

	function __construct($config = array())
	{
		if (count($config) === 0)
		{
			$config = require(ASSETIX_PATH.'/config/assetix.php');
		}
		$this->_config = $config;
		$this->_compiler = new Compiler($config);
	}

	function _get_asset_args()
	{
		$arg_count = func_num_args();
		$args = func_get_args();

		$group = array_shift($args);
		$type = array_pop($args);

		if ($arg_count === 2)
		{
			$files = array();
			$raw = false;
		}
		else if ($arg_count === 3)
		{
			if (is_bool($args[0]))
			{
				$files = array();
				$raw = $args[0];
			}
			else if (is_array($args[0]))
			{
				$files = $args[0];
				$raw = false;
			}
			else
			{
				throw new \Exception("Argument 3 must be an array of files or a bool.");
			}
		}
		else
		{
			$files = $args[0];
			$raw = $args[1];
		}

		return array($type, $group, $files, $raw);
	}

	protected function _asset()
	{
		$args = func_get_args();
		list($type, $group, $files, $raw) = call_user_func_array(
			array($this, "_get_asset_args"), $args);

		$compiled = $this->_compiler->{$type}($group, $files);

		return $this->_render($group, $type, $compiled, $raw);
	}

	// Get a css asset
	function css()
	{
		$args = func_get_args();
		$args[] = 'css';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a less asset
	function less()
	{
		$args = func_get_args();
		$args[] = 'less';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a js asset
	function js()
	{
		$args = func_get_args();
		$args[] = 'js';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a underscore asset
	function underscore()
	{
		$args = func_get_args();
		$args[] = 'underscore';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	protected function _render($group, $type, $contents, $raw = false)
	{
		if ($raw === true)
		{
			return $contents;
		}
		else
		{
			call_user_func_array(array($this, "write"), func_get_args());
		}
	}

	function get_config()
	{
		return $this->_config;
	}

	function get_version()
	{
		return $this->_config['assets_version'];
	}

	function get_debug()
	{
		return $this->_config['debug'];
	}

	function get_ext($type)
	{
		switch ($type)
		{
			case "css":
				return "css";
			case "less":
				return "css";
			case "js":
				return "js";
			case "underscore":
				return "js";
			default:
				throw \Exception("Type $type does not has an extension");
		}
	}

	function write($group, $type, $contents)
	{
		$path = ASSETIX_ASSET_PATH."/production/{$group}.".$this->get_ext($type);

		if (!is_dir($dir = dirname($path)) && false === @mkdir($dir, 0777, true)) {
            throw new \Exception('Unable to create directory '.$dir);
        }

        if (false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException('Unable to write file '.$path);
        }
	}
}