<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\Loader;

use Combyna\Component\Config\NodeVisitor;

/**
 * Class ArrayWalker
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArrayWalker
{
    /**
     * @var NodeVisitor
     */
    private $nodeVisitor;

    /**
     * @param NodeVisitor $nodeVisitor
     */
    public function __construct(NodeVisitor $nodeVisitor)
    {
        $this->nodeVisitor = $nodeVisitor;
    }

    /**
     * Walks the array, performing any pre-processing
     *
     * @param array $array
     * @return array
     */
    public function walk(array $array)
    {
        return $this->walkNode($array);
    }

    /**
     * Walks a node in the array recursively
     *
     * @param mixed|array $node
     * @return mixed|array
     */
    private function walkNode($node)
    {
        if (is_array($node)) {
            foreach ($node as $key => $childNode) {
                $node[$key] = $this->walkNode($childNode);
            }

            return $node;
        }

        return $this->nodeVisitor->visit($node);
    }
}
