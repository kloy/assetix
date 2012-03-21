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
    private $replacement;
    private $pattern;

    public function __construct($replacement = '/foo/', $pattern = '../')
    {
        $this->replacement = $replacement;
        $this->pattern = $pattern;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $replacement = $this->getReplacement();
        $pattern = $this->getPattern();
        $content = $asset->getContent();
        $content = str_replace($pattern, $replacement, $content);

        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function setReplacement($replacement = '')
    {
        $this->replacement = $replacement;
    }

    public function getReplacement()
    {
        return $this->replacement;
    }

    public function setPattern($regex = '')
    {
        $this->pattern = $regex;
    }

    public function getPattern()
    {
        return $this->pattern;
    }
}
