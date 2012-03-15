<?php

namespace Assetix;

// Composer autoloader
require 'vendor/.composer/autoload.php';

// Use Assetic namespaces
use Assetic\Asset\AssetCollection;
use Assetic\AssetManager;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\FilterManager;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Sass\SassFilter;
use Assetic\Filter\Yui;
use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;

define('BASEPATH', __DIR__);
define('ASSET_PATH', BASEPATH.'/assets');

class Assetix
{
	// Assetic AssetManager
	protected $am = null;
	// Assetic FilterManager
	protected $fm = null;
	protected $assets = array();

	function __construct()
	{
		$this->am = new AssetManager();
		$this->fm = new FilterManager();
		$this->_setup_filters();
	}

	function get_am()
	{
		return $this->am;
	}

	function get_fm()
	{
		return $this->fm;
	}

	function _to_collection($files)
	{
		$collection = new AssetCollection();

		foreach($files as $file)
		{
			if (strpos($file, '*') !== -1)
			{
				$asset = new FileAsset(ASSET_PATH.$file);
			}
			else
			{
				$asset = new GlobAsset(ASSET_PATH.$file);
			}

			$collection->add($asset);
		}

		return $collection;
	}

	// All asset methods use this logic for checking variables and adding assets
	protected function _asset($group, $files)
	{
		if ($group === '') throw new \Exception("group must be defined");
		if (! is_array($files)) throw new \Exception("files must be an array");

		if (count($files) !== 0)
		{
			$collection = $this->_to_collection($files);
			$this->add_asset($group, $collection);
		}
	}

	// Get a css asset
	function css($group = '', $files = array())
	{
		$this->_asset($group, $files);

		$assets = array('@'.$group);
		$filters = array('?yui_css');

		return $this->_render($assets, $filters);
	}

	protected function _render($assets = array(), $filters = array())
	{
		// Setup AssetFactory
		$factory = new AssetFactory(ASSET_PATH);
		$factory->setAssetManager($this->get_am());
		$factory->setFilterManager($this->get_fm());
		$factory->setDebug(true);

		return $factory->createAsset($assets, $filters)->dump();
	}

	function add_asset()
	{
		if (count(func_num_args()) === 0) throw new \Exception("asset cannot be empty");
		$args = func_get_args();
		call_user_func_array(array($this->am, "set"), $args);
	}

	function add_filter()
	{
		if (count(func_num_args()) === 0) throw new \Exception("filter cannot be empty");
		$args = func_get_args();
		call_user_func_array(array($this->fm, "set"), $args);
	}

	protected function _setup_filters()
	{
		$this->add_filter('yui_js', new Yui\JsCompressorFilter(BASEPATH.'/bin/yuicompressor-2.4.7.jar'));
		$this->add_filter('yui_css', new Yui\CssCompressorFilter(BASEPATH.'/bin/yuicompressor-2.4.7.jar'));
		$this->add_filter('less', new LessFilter());
	}
}