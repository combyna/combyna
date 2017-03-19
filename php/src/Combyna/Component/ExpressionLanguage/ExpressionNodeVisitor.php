<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\ExpressionLanguage;

use Combyna\Component\Config\NodeVisitorInterface;

/**
 * Class ExpressionNodeVisitor
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionNodeVisitor implements NodeVisitorInterface
{
    /**
     * @var ExpressionParser
     */
    private $expressionParser;

    /**
     * @param ExpressionParser $expressionParser
     */
    public function __construct(ExpressionParser $expressionParser)
    {
        $this->expressionParser = $expressionParser;
    }

    /**
     * Visits a node and parses it to an expression array structure, if specified
     *
     * @param mixed $node
     * @return mixed|array
     */
    public function visit($node)
    {
        if (!is_string($node)) {
            return $node;
        }

        if (strpos($node, '!expr') === false) {
            return $node;
        }

        $expression = substr($node, 5);

        return $this->expressionParser->parse($expression);
    }
}
