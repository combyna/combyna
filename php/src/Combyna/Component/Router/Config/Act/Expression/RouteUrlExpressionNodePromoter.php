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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodePromoterInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeTypePromoterInterface;
use Combyna\Component\Router\Expression\RouterExpressionFactoryInterface;
use InvalidArgumentException;

/**
 * Class RouteUrlExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteUrlExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var RouterExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionNodePromoterInterface
     */
    private $expressionNodePromoter;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param RouterExpressionFactoryInterface $expressionFactory
     * @param ExpressionNodePromoterInterface $expressionNodePromoter
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        RouterExpressionFactoryInterface $expressionFactory,
        ExpressionNodePromoterInterface $expressionNodePromoter
    ) {
        $this->bagFactory = $bagFactory;
        $this->expressionFactory = $expressionFactory;
        $this->expressionNodePromoter = $expressionNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [RouteUrlExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof RouteUrlExpressionNode) {
            throw new InvalidArgumentException(sprintf(
                'Expected a "%s" expression node, got "%s"',
                RouteUrlExpressionNode::TYPE,
                $expressionNode->getType()
            ));
        }

        return $this->expressionFactory->createRouteUrlExpression(
            $this->expressionNodePromoter->promote($expressionNode->getRouteNameExpression()),
            $expressionNode->getRouteArgumentStructureExpression() !== null ?
                $this->expressionNodePromoter->promote($expressionNode->getRouteArgumentStructureExpression()) :
                $this->expressionFactory->createStructureExpression(
                    $this->bagFactory->createExpressionBag([])
                )
        );
    }
}
