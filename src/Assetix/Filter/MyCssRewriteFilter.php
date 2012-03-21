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

    // Replacement to use when pattern is matched during css rewrite
    // //1 concats the first group matched i nthe pattern to the string, e. g.
    // ' or "
    // Pattern to match when rewriting css
    // Pattern matches ../ recursively, safely working with or without quotes.
    // This is used to swap out relative paths in a css file with a path by default.
    // It of course could be used for rewriting anything in a css file however.
    public function __construct($replacement = '\\1/assets/production/', $pattern = '/(url\((\"|\'|))(\.\.\/)+/')
    {
        $this->replacement = $replacement;
        $this->pattern = $pattern;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $replacement = $this->getReplacement();
        $pattern = $this->getPattern();
        $content = $asset->getContent();
        $content = preg_replace($pattern, $replacement, $content);

        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }

    public function setReplacement($replacement)
    {
        $this->replacement = $replacement;
    }

    public function getReplacement()
    {
        return $this->replacement;
    }

    public function setPattern($regex)
    {
        $this->pattern = $regex;
    }

    public function getPattern()
    {
        return $this->pattern;
    }
}
