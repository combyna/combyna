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

use Combyna\Component\Expression\Config\Act\Assurance\DelegatingAssuranceNodePromoter;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use InvalidArgumentException;

/**
 * Class GuardExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class GuardExpressionNodePromoter implements ExpressionNodeTypePromoterInterface
{
    /**
     * @var DelegatingAssuranceNodePromoter
     */
    private $assuranceNodePromoter;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var DelegatingExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param DelegatingExpressionNodePromoter $expressionNodePromoter
     * @param DelegatingAssuranceNodePromoter $assuranceNodePromoter
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        DelegatingExpressionNodePromoter $expressionNodePromoter,
        DelegatingAssuranceNodePromoter $assuranceNodePromoter
    ) {
        $this->assuranceNodePromoter = $assuranceNodePromoter;
        $this->expressionFactory = $expressionFactory;
        $this->expressionNodePromoter = $expressionNodePromoter;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypes()
    {
        return [GuardExpressionNode::TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!$expressionNode instanceof GuardExpressionNode) {
            throw new InvalidArgumentException(
                'Expected a "guard" expression node, got "' . $expressionNode->getType() . '"'
            );
        }

        return $this->expressionFactory->createGuardExpression(
            $this->assuranceNodePromoter->promoteCollection($expressionNode->getAssuranceNodes()),
            $this->expressionNodePromoter->promote($expressionNode->getConsequentExpression()),
            $this->expressionNodePromoter->promote($expressionNode->getAlternateExpression())
        );
    }
}
