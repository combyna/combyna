<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config;

use Combyna\Component\Common\DelegatorInterface;

/**
 * Class NodeVisitor
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NodeVisitor implements NodeVisitorInterface
{
    /**
     * @var NodeVisitorInterface[]
     */
    private $visitors = [];

    /**
     * Adds a new visitor to be called for every visited node
     *
     * @param NodeVisitorInterface $visitor
     */
    public function addVisitor(NodeVisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }

    /**
     * Visits a node and parses it to an expression array structure, if specified
     *
     * @param mixed $node
     * @return mixed|array
     */
    public function visit($node)
    {
        foreach ($this->visitors as $visitor) {
            // Apply each visitor in sequence
            $node = $visitor->visit($node);
        }

        return $node;
    }
}
