# Assetix

A simple wrapper for easily using Assetic. Provides a simple way for including assets and
letting Assetic build and cache them in production.

Assetix uses [composer](http://getcomposer.org/) for including
[Assetic](https://github.com/kriswallsmith/assetic). Follow the steps at
[Packagist](http://packagist.org/) if you are unfamiliar with composer to get going.

## Example

	require_once('classes/assetix.php');

	use Assetix\Assetix;

	// Instantiate Assetix
	$assetix = new Assetix();

	// Add css files to group base_css
	$css = array('/css/test.css');
	$assetix->css('base_css', $css);

	// Add js files to group base_js
	$js = array('/js/underscore.js', '/js/*');
	$assetix->js('base_js', $js);

	// Echo out raw compiled files
	echo $assetix->css('base_css', true)."\n";
	echo $assetix->js('base_js', true)."\n";

	// Echo out links to compiled files
	echo $assetix->css('base_css')."\n";
	echo $assetix->js('base_js')."\n";

## Installation

Follow the instructions on [Packagist](http://packagist.org/) to add
"kloy/assetix": "0.1.*" in the require section of your composer.json file and install.
Once installed run `npm install .` from the vendor/kloy/assetix/ dir. This will install
the needed nodejs modules for compiling less, stylus, and coffeescript.

## Available asset function

Assetix features the ability to compile many assets. All functions implement the same
argument structure. This structure is
`func('group', array() /* asset paths */, bool /* true for raw outputting, false for a link */)`.
As you may have noticed, you can call func() without an array of assets if you just wish to
get a link from a previously defined group. You may call it with a second argument of true
to get the raw output of the group. The current list of asset functions is below...

*	css() - normal css stylesheet
*	less() - less css
*	styl() - stylus css
*	js() - normal javascript
*	coffee() - coffee-script
*	underscore() - underscore javascript templates
*	handlebars() - handlebars javascript templates

## How does the build process work?

The assets are lazily built on the first request for them. Once built they are cached so
further builds for the same asset is not needed. In a production setting you will assign
a version number in the assetix config. When this version number is changed all assets are
rebuilt and a new file with this version number is generated for cache busting purposes.

Assets that become css are first compiled to css, then images are embedded into the css
via datauri and base64 encoding, last the assets get minimized. Assets that become js are
compiled to js and minimized.

## Internet Explorer Version 7 and below.

IE 7 and below does not support datauri functionality used for embedding images into css.
In order to work around this you can prefix 'ie_' to a group's name. Here is an example.

`css('ie_foo', array('css/*'));`

This will cause the compiler to not use the embedcss filter which means all images will be
requested as normal.

## API

The public available APIs are described in interfaces. Currently the iAssetix and iCompiler
interfaces exist. This project follows [Semantic Versioning](http://semver.org/).