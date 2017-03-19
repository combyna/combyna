<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Act;

use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodePromoter;
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
     * @var AssuranceNodePromoter
     */
    private $assuranceNodePromoter;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionNodePromoter
     */
    private $expressionNodePromoter;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionNodePromoter $expressionNodePromoter
     * @param AssuranceNodePromoter $assuranceNodePromoter
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionNodePromoter $expressionNodePromoter,
        AssuranceNodePromoter $assuranceNodePromoter
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
