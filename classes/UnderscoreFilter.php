<?php

namespace Assetix\Filter;

require_once(ASSETIX_PATH.'/vendor/.composer/autoload.php');

use Assetic\Filter\FilterInterface;
use Assetic\Asset\AssetInterface;
use Assetic\Util\ProcessBuilder;

/**
 * Loads Underscore JS Template files.
 *
 * @author Keith Loy <ckeithloy@gmail.com>
 */
class UnderscoreFilter implements FilterInterface
{
    private $underscoreTmplPath;
    private $nodePath;

    public function __construct($underscoreTmplPath = "", $nodePath = '/usr/bin/node')
    {
        $this->underscoreTmplPath = ASSETIX_PATH.'/bin/_tmpl';
        $this->nodePath = $nodePath;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $template_name = $asset->getSourcePath();
        $template_contents = addslashes(
            preg_replace("/[\n\r\t ]+/"," ",$asset->getContent())
        );
        $content = 'window.JST = window.JST || {};'
                    . PHP_EOL
                    . "window.JST['{$template_name}'] = _.template("
                    . "'{$template_contents}');"
                    . PHP_EOL;

        $asset->setContent($content);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
