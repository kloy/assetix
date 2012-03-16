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
        $input = tempnam(sys_get_temp_dir(), 'assetic_underscore_tmpl');
        file_put_contents($input, $asset->getContent());

        $pb = new ProcessBuilder(array(
            $this->nodePath,
            $this->underscoreTmplPath,
            $input,
        ));

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $processedContent = "JST['".$asset->getSourcePath()."'] = ".$proc->getOutput().";";
        $asset->setContent($processedContent);
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
