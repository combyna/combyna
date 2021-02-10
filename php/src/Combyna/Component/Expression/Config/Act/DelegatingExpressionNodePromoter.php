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

use Combyna\Component\Common\Delegator\DelegatorInterface;
use InvalidArgumentException;

/**
 * Class DelegatingExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingExpressionNodePromoter implements ExpressionNodePromoterInterface, DelegatorInterface
{
    /**
     * @var ExpressionNodeTypePromoterInterface[]
     */
    private $typePromoters = [];

    /**
     * {@inheritdoc}
     */
    public function addPromoter(ExpressionNodeTypePromoterInterface $typePromoter)
    {
        foreach ($typePromoter->getTypes() as $type) {
            if (array_key_exists($type, $this->typePromoters)) {
                throw new InvalidArgumentException(
                    'An expression promoter of type "' . $type . '" is already registered'
                );
            }

            $this->typePromoters[$type] = $typePromoter;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function promote(ExpressionNodeInterface $expressionNode)
    {
        if (!array_key_exists($expressionNode->getType(), $this->typePromoters)) {
            throw new InvalidArgumentException(
                'No expression promoter of type "' . $expressionNode->getType() . '" is registered'
            );
        }

        return $this->typePromoters[$expressionNode->getType()]->promote($expressionNode);
    }
}
