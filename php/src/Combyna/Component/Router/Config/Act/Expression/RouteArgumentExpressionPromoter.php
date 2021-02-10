<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act\Expression;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeTypePromoterInterface;
use Combyna\Component\Router\Expression\RouterExpressionFactoryInterface;
use InvalidArgumentException;

/**
 * Class RouteArgumentExpressionPromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteArgumentExpressionPromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var RouterExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param RouterExpressionFactoryInterface $expressionFactory
     */
    public function __construct(RouterExpressionFactoryInterface $expressionFactory)
    {
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [RouteArgumentExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof RouteArgumentExpressionNode) {
            throw new InvalidArgumentException(sprintf(
                'Expected a "%s" expression node, got "%s"',
                RouteArgumentExpressionNode::TYPE,
                $expressionNode->getType()
            ));
        }

        return $this->expressionFactory->createRouteArgumentExpression($expressionNode->getRouteParameterName());
    }
}
