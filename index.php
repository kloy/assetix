<?php

// Setup AssetManager and return the instance
function get_asset_manager()
{
	return new AssetManager();
}

// Setup filter manager and return the instance
function get_filter_manager()
{
	return new FilterManager();
}

// Return a new AssetWriter instance
function get_writer()
{
	return new AssetWriter('/path/to/web');
}

function main()
{
	$base_js_collection = new AssetCollection(array(
		new FileAsset(ASSET_PATH.'/js/underscore.js'),
		new GlobAsset(ASSET_PATH.'/js/*'),
	));

	$base_css_collection = new AssetCollection(array(
		new FileAsset(ASSET_PATH.'/less/test.less'),
	));

	$am = get_asset_manager();
	$am->set('base_js', $base_js_collection);
	$am->set('base_css', $base_css_collection);

	$fm = get_filter_manager();
	$fm->set('yui_js', new Yui\JsCompressorFilter(BASEPATH.'/bin/yuicompressor-2.4.7.jar'));
	$fm->set('yui_css', new Yui\CssCompressorFilter(BASEPATH.'/bin/yuicompressor-2.4.7.jar'));
	$fm->set('less', new LessFilter());

	// Setup AssetFactory
	$factory = new AssetFactory(ASSET_PATH);
	$factory->setAssetManager($am);
	$factory->setFilterManager($fm);
	$factory->setDebug(true);

	$js_assets = array(
		'@base_js', // load the asset manager's "base_js" asset
	);

	$js_filters = array(
	    '?yui_js',
	);

	$js = $factory->createAsset($js_assets, $js_filters);

	// echo $js->dump()."\n";

	$css_assets = array(
		'@base_css',
	);
	$css_filters = array('less', '?yui_css');

	$css = $factory->createAsset($css_assets, $css_filters);
	echo $css->dump()."\n";

	// $writer->writeManagerAssets($am);
}

main();
exit;