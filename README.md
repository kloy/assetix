# Assetix

A simple wrapper for easily using Assetic. Project is still in heavy development, but the
end goal is to provide a simple way for including assets and letting Assetic build and
cache them in production.

Assetix uses [composer](http://getcomposer.org/) for including Assetic. Follow the steps
at [Packagist](http://packagist.org/) if you are unfamiliar with composer to get going.

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
	echo $assetix->css('base_css', array(), true)."\n";
	echo $assetix->js('base_js', array(), true)."\n";

