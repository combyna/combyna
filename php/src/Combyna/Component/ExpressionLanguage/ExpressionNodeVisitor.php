<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\ExpressionLanguage;

use Combyna\Component\Config\NodeVisitorInterface;
use Combyna\Component\ExpressionLanguage\Config\Act\UnparsableExpressionNode;
use InvalidArgumentException;

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

        $expression = trim(substr($node, 5));

        try {
            return $this->expressionParser->parse($expression);
        } catch (InvalidArgumentException $exception) {
            // Return a special "unparsable expression" for validation to fail with
            return [
                'type' => UnparsableExpressionNode::TYPE,
                'expression' => $expression
            ];
        }
    }
}
