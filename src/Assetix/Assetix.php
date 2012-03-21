<?php
/**
 * Assetix.
 *
 * @package    Assetix
 * @version    0.1.1
 * @author     Keith Loy
 * @license    MIT License
 * @copyright  2012 Keith Loy
 * @link       http://keithloy.me
 */

namespace Assetix;

interface iAssetix
{
	/**
	 * Returns a link to the asset file or the raw output of the assets.
	 * css, less, styl, js, coffee, underscore, handlebars
	 *
	 * @param string $group group to use for file name and later references
	 * @param array $files optional array of files to be passed for compilation
	 * @param bool $raw optional last argument for specifying raw output of assets
	 *
	 * @return string link to file or raw output
	 */
	public function css();
	public function less();
	public function styl();
	public function js();
	public function coffee();
	public function underscore();
	public function handlebars();
}

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
class Assetix implements iAssetix
{
	// Config array
	protected $_config = array();
	// Instance of Compiler
	protected $_compiler = null;

	// Constructor. Takes and array of config settings.
	public function __construct($config = array())
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
			// Pattern to match when rewriting css
			// Pattern matches ../ recursively, safely working with or without quotes.
			// This is used to swap out relative paths in a css file with a path by default.
			// It of course could be used for rewriting anything in a css file however.
			'css_rewrite_pattern' => '/(\((\"|\'|))(\.\.\/)*/',
			// Replacement to use when pattern is matched during css rewrite
			// //1 concats the first group matched i nthe pattern to the string, e. g.
			// ' or "
			'css_rewrite_replacement' => '\\1/assets/production/',
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
	public function css()
	{
		$args = func_get_args();
		$args[] = 'css';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a less asset
	public function less()
	{
		$args = func_get_args();
		$args[] = 'less';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a stylus asset
	public function styl()
	{
		$args = func_get_args();
		$args[] = 'styl';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a js asset
	public function js()
	{
		$args = func_get_args();
		$args[] = 'js';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a coffee-script asset
	public function coffee()
	{
		$args = func_get_args();
		$args[] = 'coffee';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a underscore asset
	public function underscore()
	{
		$args = func_get_args();
		$args[] = 'underscore';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	// Get a handlebars asset
	public function handlebars()
	{
		$args = func_get_args();
		$args[] = 'handlebars';

		return call_user_func_array(array($this, "_asset"), $args);
	}

	public function clear_cache()
	{
		$path = $this->_get_cache_path().'/*';
		array_map("unlink", glob($path));
	}

	public function clear_production()
	{
		// output_absolute_path
		$path = $this->_get_absolute_path().'/*';
		array_map("unlink", glob($path));
	}

	public function set_rewrite($replacement, $pattern = null)
	{
		$this->_compiler->set_css_rewrite_replacement($replacement);
		if ($pattern !== null)
		{
			$this->_compiler->set_css_rewrite_pattern($pattern);
		}
	}

	// Redundant asset logic
	protected function _asset()
	{
		$args = func_get_args();
		list($type, $group, $files, $raw, $is_ie) = call_user_func_array(
			array($this, "_get_asset_args"), $args);

		$asset_path = "/{$group}-".$this->_get_version().".".$this->_determine_ext($type);
		$path = $this->_get_absolute_path().$asset_path;

		if ( ! $raw and ! $this->_is_debug() and is_file($path))
		{
			$asset = $this->_get_path().$asset_path;
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
			if ($this->_is_debug())
			{
				$version = md5($contents);
			}
			else
			{
				$version = $this->_get_version();
			}

			$asset_path = "/{$group}-{$version}.".$this->_determine_ext($type);
			$this->_write($this->_get_absolute_path().$asset_path, $contents);

			return $this->_get_path().$asset_path;
		}
	}

	// Returns the config
	protected function _get_config()
	{
		return $this->_config;
	}

	protected function _get_cache_path()
	{
		return $this->_config['cache_path'];
	}

	// Returns the output_absolute_path config setting
	protected function _get_absolute_path()
	{
		return $this->_config['output_absolute_path'];
	}

	// Returns the output_path config setting
	protected function _get_path()
	{
		return $this->_config['output_path'];
	}

	// Returns the version config setting
	protected function _get_version()
	{
		return $this->_config['assets_version'];
	}

	// Returns the debug config setting
	protected function _is_debug()
	{
		return $this->_config['debug'];
	}

	// Takes a type and returns the appropriate extension
	protected function _determine_ext($type)
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
	protected function _write($path, $contents)
	{
		if (!is_dir($dir = dirname($path)) && false === @mkdir($dir, 0777, true)) {
            throw new \Exception('Unable to create directory '.$dir);
        }

        if (false === @file_put_contents($path, $contents)) {
            throw new \RuntimeException('Unable to write file '.$path);
        }
	}
}