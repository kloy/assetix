<?php

namespace Assetix;

// Composer autoloader
require_once(ASSETIX_PATH.'/vendor/.composer/autoload.php');
require_once(ASSETIX_PATH.'/classes/UnderscoreFilter.php');

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
use Assetix\Filter\UnderscoreFilter;
use Assetic\Factory\AssetFactory;

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
				$asset = new FileAsset(ASSETIX_ASSET_PATH.$file);
			}
			else
			{
				$asset = new GlobAsset(ASSETIX_ASSET_PATH.$file);
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
	function css($group = '', $files = array())
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array('?yui_css', '?css_embed');

		return $this->_render($assets, $filters);
	}

	// Get a less asset
	function less($group = '', $files = array())
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array('less', '?yui_css', '?css_embed');

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

	// Get a underscore template asset
	function underscore($group = '', $files = array())
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array('underscore', '?yui_js');

		return $this->_render($assets, $filters);
	}

	protected function _render($assets = array(), $filters = array())
	{
		// Setup AssetFactory
		$factory = new AssetFactory(ASSETIX_ASSET_PATH);
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

	function write($contents)
	{
		$this->_aw->write(ASSETIX_PATH.'/production', $contents);
	}

	protected function _setup_filters()
	{
		$config = $this->get_config();
		$this->add_filter('yui_js', new Yui\JsCompressorFilter($config['yuicompressor_path']));
		$this->add_filter('yui_css', new Yui\CssCompressorFilter($config['yuicompressor_path']));
		$this->add_filter('less', new LessFilter($config['node_path']));
		$css_embed = new CssEmbedFilter($config['cssembed_path']);
		$css_embed->setRoot($config['cssembed_root']);
		$css_embed->setMhtml(false);
		$css_embed->setCharset('utf8');
		$this->add_filter('css_embed', $css_embed);
		$this->add_filter('underscore', new UnderscoreFilter($config['node_path']));
	}
}