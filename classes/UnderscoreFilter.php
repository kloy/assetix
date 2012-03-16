<?php

namespace Assetix\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Util\ProcessBuilder;

/**
 * Loads Underscore JS Template files.
 *
 * @author Keith Loy <ckeithloy@gmail.com>
 */
class UnderscoreFilter implements FilterInterface
{
    private $nodeBin;
    private $nodePaths;

    /**
     * Constructor.
     *
     * @param string $nodeBin   The path to the node binary
     * @param array  $nodePaths An array of node paths
     */
    public function __construct($nodeBin = '/usr/bin/node', array $nodePaths = array())
    {
        $this->nodeBin = $nodeBin;
        $this->nodePaths = $nodePaths;
    }

    public function filterLoad(AssetInterface $asset)
    {
        static $format = <<<'EOF'
var less = require('less');
var sys  = require(process.binding('natives').util ? 'util' : 'sys');

new(less.Parser)(%s).parse(%s, function(e, tree) {
    if (e) {
        less.writeError(e);
        process.exit(2);
    }

    try {
        sys.print(tree.toCSS(%s));
    } catch (e) {
        less.writeError(e);
        process.exit(3);
    }
});

EOF;

        $root = $asset->getSourceRoot();
        $path = $asset->getSourcePath();

        // parser options
        $parserOptions = array();
        if ($root && $path) {
            $parserOptions['paths'] = array(dirname($root.'/'.$path));
            $parserOptions['filename'] = basename($path);
        }

        // tree options
        $treeOptions = array();

        $pb = new ProcessBuilder();
        $pb->inheritEnvironmentVariables();

        // node.js configuration
        if (0 < count($this->nodePaths)) {
            $pb->setEnv('NODE_PATH', implode(':', $this->nodePaths));
        }

        $pb->add($this->nodeBin)->add($input = tempnam(sys_get_temp_dir(), 'assetic_underscore'));
        file_put_contents($input, sprintf($format,
            json_encode($parserOptions),
            json_encode($asset->getContent()),
            json_encode($treeOptions)
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
