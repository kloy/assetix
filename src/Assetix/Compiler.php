<?php

/**
 * Assetix.
 *
 * @package    Assetix
 * @version    0.0.1
 * @author     Keith Loy
 * @license    MIT License
 * @copyright  2012 Keith Loy
 * @link       http://keithloy.me
 */

namespace Assetix;

// Use Assetic namespaces
use Assetic\Asset\AssetCollection;
use Assetic\Asset\AssetCache;
use Assetic\Cache\FilesystemCache;
use Assetic\AssetManager;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\FilterManager;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Sass\SassFilter;
use Assetic\Filter\Yui;
use Assetic\Filter\CssEmbedFilter;
use Assetic\Filter\CoffeeScriptFilter;
use Assetic\Filter\StylusFilter;
use Assetix\Filter\UnderscoreFilter;
use Assetic\Factory\AssetFactory;

/**
 * Assetix Compiler Class
 *
 * Abstracts out usage of Assetic allowing for assets to be compiled with pre-configured
 * Assetic filters.
 *
 * @package     Assetix
 * @category    Assetix
 * @author      Keith Loy
 */
class Compiler
{
	// Assetic AssetManager
	protected $_am = null;
	// Assetic FilterManager
	protected $_fm = null;
	protected $_assets = array();
	protected $_config = array();

	function __construct($config = array())
	{
		$this->_config = $config;
		$this->_am = new AssetManager();
		$this->_fm = new FilterManager();
		$this->_setup_filters();
	}

	function get_am()
	{
		return $this->_am;
	}

	function get_fm()
	{
		return $this->_fm;
	}

	protected function _to_collection($files)
	{
		$collection = new AssetCollection();

		foreach($files as $file)
		{
			if (strpos($file, '*') === false)
			{
				$asset = new FileAsset($this->get_asset_path().$file);
			}
			else
			{
				$asset = new GlobAsset($this->get_asset_path().$file);
			}

			$collection->add($asset);
		}

		return $collection;
	}

	protected function _to_cache(AssetCollection $collection)
	{
		$config = $this->get_config();
		$system_cache = new FilesystemCache($config['cache_path']);

		$cached = new AssetCache($collection, $system_cache);

		return $cached;
	}

	// All asset methods use this logic for checking variables and adding assets
	protected function _asset($group, $files)
	{
		if ($group === '') throw new \Exception("group must be defined");
		if (! is_array($files)) throw new \Exception("files must be an array");

		if (count($files) !== 0)
		{
			$collection = $this->_to_collection($files);
			$cached = $this->_to_cache($collection);
			$this->add_asset($group, $cached);
		}
	}

	// Get a css asset
	function css($group = '', $files = array(), $is_ie = false)
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array();
		if ($is_ie === false) $filters[] = '?css_embed';
		$filters[] = '?yui_css';

		return $this->_render($assets, $filters);
	}

	// Get a less asset
	function less($group = '', $files = array(), $is_ie = false)
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array();
		$filters[] = 'less';
		if ($is_ie === false) $filters[] = '?css_embed';
		$filters[] = '?yui_css';

		return $this->_render($assets, $filters);
	}

	// Get a stylus asset
	function styl($group = '', $files = array(), $is_ie = false)
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array();
		$filters[] = 'styl';
		if ($is_ie === false) $filters[] = '?css_embed';
		$filters[] = '?yui_css';

		return $this->_render($assets, $filters);
	}

	// Get a js asset
	function js($group = '', $files = array())
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array('?yui_js');

		return $this->_render($assets, $filters);
	}

	// Get a coffee asset
	function coffee($group = '', $files = array())
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array('coffee', '?yui_js');

		return $this->_render($assets, $filters);
	}

	// Get a underscore template asset
	function underscore($group = '', $files = array())
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array('underscore', '?yui_js');
		$rendered = $this->_render($assets, $filters);
		$config = $this->get_config();

		$ns = $config['underscore_namespace'];
		return "var {$ns} = {$ns} || {};".PHP_EOL.$rendered;
	}

	protected function _render($assets = array(), $filters = array())
	{
		// Setup AssetFactory
		$factory = new AssetFactory($this->get_asset_path());
		$factory->setAssetManager($this->get_am());
		$factory->setFilterManager($this->get_fm());
		$config = $this->get_config();
		$factory->setDebug($config['debug']);

		return $factory->createAsset($assets, $filters)->dump();
	}

	function add_asset()
	{
		if (count(func_num_args()) === 0) throw new \Exception("asset cannot be empty");
		$args = func_get_args();
		call_user_func_array(array($this->_am, "set"), $args);
	}

	function add_filter()
	{
		if (count(func_num_args()) === 0) throw new \Exception("filter cannot be empty");
		$args = func_get_args();
		call_user_func_array(array($this->_fm, "set"), $args);
	}

	function get_config()
	{
		return $this->_config;
	}

	function get_asset_path()
	{
		return $this->_config['asset_path'];
	}

	function get_absolute_path()
	{
		return $this->_config['output_absolute_path'];
	}

	function write($contents)
	{
		$this->_aw->write($this->get_absolute_path(), $contents);
	}

	protected function _setup_filters()
	{
		$config = $this->get_config();
		$this->add_filter('yui_js', new Yui\JsCompressorFilter($config['yuicompressor_path']));
		$this->add_filter('yui_css', new Yui\CssCompressorFilter($config['yuicompressor_path']));
		$this->add_filter('less', new LessFilter($config['node_path'], $config['node_paths']));
		$this->add_filter('styl', new StylusFilter($config['node_path'], $config['node_paths']));
		$css_embed = new CssEmbedFilter($config['cssembed_path']);
		if ($config['cssembed_root'] !== false)
		{
			$css_embed->setRoot($config['cssembed_root']);
		}
		$css_embed->setMhtml(false);
		$css_embed->setCharset('utf8');
		$this->add_filter('css_embed', $css_embed);
		$this->add_filter('underscore', new UnderscoreFilter(
			$config['underscore_namespace']), $config['underscore_ext']);
		$this->add_filter('coffee', new CoffeeScriptFilter(
			$config['coffee_path'], $config['node_path']));
	}
}