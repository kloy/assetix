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
