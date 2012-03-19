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

define('ASSETIXPATH', realpath(__DIR__).DIRECTORY_SEPARATOR);
require(ASSETIXPATH.'vendor/.composer/autoload.php');

/**
 * Fuel autoloader
 */
Autoloader::add_core_namespace('Assetix');

Autoloader::add_classes(array(
    'Assetix\\Assetix' => __DIR__.'/src/Assetix/Assetix.php',
    'Assetix\\Compiler' => __DIR__.'/src/Assetix/Compiler.php',
    'Assetix\\Filter\UnderscoreFilter' => __DIR__.'/src/Assetix/Filter/UnderscoreFilter.php',
));

/* End of file bootstrap.php */