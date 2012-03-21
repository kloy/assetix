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

namespace Assetix\Filter;

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Util\ProcessBuilder;

/**
 * Replaces relative url's with passed path.
 *
 * @link http://handlebarsjs.com/precompilation.html
 * @author Keith Loy <ckeithloy@gmail.com>
 */
class MyCssRewriteFilter implements FilterInterface
{
    private $urlPath;

    public function __construct($urlPath = '/foo/')
    {
        $this->urlPath = $urlPath;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $path = $this->getUrlPath();
        $content = $asset->getContent();
        $content = str_replace('../', $path, $content);

        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function setUrlPath($urlPath = '')
    {
        $this->urlPath = $urlPath;
    }

    public function getUrlPath()
    {
        return $this->urlPath;
    }
}
