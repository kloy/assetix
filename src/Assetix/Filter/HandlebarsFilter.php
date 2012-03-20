<?php

/**
 * Assetix.
 *
 * @package    Assetix
 * @version    0.1.0
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
 * Compiles Handlebars into Javascript.
 *
 * @link http://handlebarsjs.com/precompilation.html
 * @author Keith Loy <ckeithloy@gmail.com>
 */
class HandlebarsFilter implements FilterInterface
{
    private $handlebarsPath;
    private $nodePath;

    public function __construct($handlebarsPath = '/usr/bin/handlebars', $nodePath = '/usr/bin/node')
    {
        $this->handlebarsPath = $handlebarsPath;
        $this->nodePath = $nodePath;
    }

    public function filterLoad(AssetInterface $asset)
    {
        $input = tempnam(sys_get_temp_dir(), 'assetic_handlebars');
        file_put_contents($input, $asset->getContent());

        $pb = new ProcessBuilder(array(
            $this->nodePath,
            $this->handlebarsPath,
            $input,
        ));

        $proc = $pb->getProcess();
        $code = $proc->run();
        unlink($input);

        if (0 < $code) {
            throw new \RuntimeException($proc->getErrorOutput());
        }

        $asset->setContent($proc->getOutput());
    }

    public function filterDump(AssetInterface $asset)
    {
    }
}
