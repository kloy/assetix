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

namespace Assetix\Filter;

require_once(ASSETIX_PATH.'/vendor/.composer/autoload.php');

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Util\ProcessBuilder;

/**
 * Assetic Filter for UnderscoreJS Templates
 *
 * @package     Assetix
 * @subpackage  Filter
 * @category    Filter
 * @author      Keith Loy
 */
class UnderscoreFilter implements FilterInterface
{
    private $namespace;
    private $ext;

    public function __construct($namespace = 'JST', $ext = '.jst')
    {
        $this->namespace = $namespace;
        $this->ext = $ext;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $namespace = $this->namespace;
        $ext = $this->ext;
        $template_name = str_replace($ext, '', $asset->getSourcePath());
        $template_contents = addslashes(
            preg_replace("/[\n\r\t ]+/"," ",$asset->getContent())
        );
        $content = "{$namespace}['{$template_name}'] = _.template("
                    . "'{$template_contents}');" . PHP_EOL;

        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
