<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\ExpressionFactoryInterface;
use InvalidArgumentException;

/**
 * Class AssuredExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     */
    public function __construct(ExpressionFactoryInterface $expressionFactory)
    {
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [AssuredExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof AssuredExpressionNode) {
            throw new InvalidArgumentException(
                'Expected a "assured" expression node, got "' . $expressionNode->getType() . '"'
            );
        }

        return $this->expressionFactory->createAssuredExpression(
            $expressionNode->getAssuredStaticName()
        );
    }
}
