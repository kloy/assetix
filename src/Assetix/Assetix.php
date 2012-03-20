<?php

/**
 * Assetix.
 *
 * @package    Assetix
 * @version    0.0.2
 * @author     Keith Loy
 * @license    MIT License
 * @copyright  2012 Keith Loy
 * @link       http://keithloy.me
 */

namespace Assetix;

/**
 * Assetix Class
 *
 * Primary point of entry for Assetix package. Provides simple "interfaces" for compiling
 * and rendering assets.
 *
 * @package     Assetix
 * @category    Assetix
 * @author      Keith Loy
 */
class Assetix
{
	// Config array
	protected $_config = array();

	// Constructor. Takes and array of config settings.
	function __construct($config = array())
	{
		$assetix_path = dirname(__FILE__).'/../..';
		$config = array_merge(array(
			// Debug mode turns off some filters such as yuicompressor.
			// Set to false for production.
			'debug' => true,
			// Path to yuicompressor
			'yuicompressor_path' => $assetix_path.'/bin/yuicompressor-2.4.7.jar',
			// Path to nodejs executable
			'node_path' => '/usr/bin/node',
			// Paths used in require by node
			'node_paths' => array($assetix_path.'/node_modules'),
			// Path to coffeescript compiler
			'coffee_path' => $assetix_path.'/node_modules/.bin/coffee',
			// Path to handlebars compiler
			'handlebars_path' => $assetix_path.'/node_modules/.bin/handlebars',
			// Path to use for asset cache
			'cache_path' => $assetix_path.'/cache',
			// Path to cssembed jar
			'cssembed_path' => $assetix_path.'/bin/cssembed-0.4.5.jar',
			// root path to convert relative uri to. set to false to just let it be relative.
			'cssembed_root' => false,
			// Javascript namespace to compile templates under
			'underscore_namespace' => 'JST',
			// Extension for underscore files
			'underscore_ext' => '.jst',
			// Absolute path to output assets to
			'output_absolute_path' => $assetix_path.'/assets/production',
			// Web path used for serving links
			'output_path' => '/assets/production',
			// Current assets version. Update to break production cache.
			'assets_version' => '0.0.1',
			// Path assets are located at
			'asset_path' => $assetix_path.'/assets',
		), $config);

		$this->_config = $config;
		$this->_compiler = new Compiler($config);
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

	// Get a stylus asset
	function styl()
	{
		$args = func_get_args();
		$args[] = 'styl';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a js asset
	function js()
	{
		$args = func_get_args();
		$args[] = 'js';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a coffee-script asset
	function coffee()
	{
		$args = func_get_args();
		$args[] = 'coffee';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a underscore asset
	function underscore()
	{
		$args = func_get_args();
		$args[] = 'underscore';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a handlebars asset
	function handlebars()
	{
		$args = func_get_args();
		$args[] = 'handlebars';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Redundant asset logic
	protected function _asset()
	{
		$args = func_get_args();
		list($type, $group, $files, $raw, $is_ie) = call_user_func_array(
			array($this, "_get_asset_args"), $args);

		$asset_path = "/{$group}-".$this->get_version().".".$this->determine_ext($type);
		$path = $this->get_absolute_path().$asset_path;

		if ( ! $raw and ! $this->is_debug() and is_file($path))
		{
			$asset = $this->get_path().$asset_path;
		}
		else
		{
			$compiled = $this->_compiler->{$type}($group, $files, $is_ie);
			$asset = $this->_render($group, $type, $compiled, $raw);
		}

		return $asset;
	}

	protected function _get_asset_args()
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

		// check if this group should have ie true passed.
		$is_ie = (substr(trim($group), 0, 3) === 'ie_') ? true : false;

		return array($type, $group, $files, $raw, $is_ie);
	}

	// Asset render logic
	protected function _render($group, $type, $contents, $raw = false)
	{
		if ($raw === true)
		{
			return $contents;
		}
		else
		{
			if ($this->is_debug())
			{
				$version = md5($contents);
			}
			else
			{
				$version = $this->get_version();
			}

			$asset_path = "/{$group}-{$version}.".$this->determine_ext($type);
			$this->write($this->get_absolute_path().$asset_path, $contents);

			return $this->get_path().$asset_path;
		}
	}

	// Returns the config
	function get_config()
	{
		return $this->_config;
	}

	// Returns the output_absolute_path config setting
	function get_absolute_path()
	{
		return $this->_config['output_absolute_path'];
	}

	// Returns the output_path config setting
	function get_path()
	{
		return $this->_config['output_path'];
	}

	// Returns the version config setting
	function get_version()
	{
		return $this->_config['assets_version'];
	}

	// Returns the debug config setting
	function is_debug()
	{
		return $this->_config['debug'];
	}

	// Takes a type and returns the appropriate extension
	function determine_ext($type)
	{
		switch ($type)
		{
			case "css":
				return "css";
			case "less":
				return "css";
			case "styl":
				return "css";
			case "js":
				return "js";
			case "underscore":
				return "js";
			case "coffee":
				return "js";
			case "handlebars":
				return "js";
			default:
				throw \Exception("Type $type does not has an extension");
		}
	}

	// Writes contents to a path
	function write($path, $contents)
	{
		if (!is_dir($dir = dirname($path)) && false === @mkdir($dir, 0777, true)) {
            throw new \Exception('Unable to create directory '.$dir);
        }

        if (false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException('Unable to write file '.$path);
        }
	}
}