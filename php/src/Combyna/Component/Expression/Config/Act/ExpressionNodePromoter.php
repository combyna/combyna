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

use Combyna\Component\Expression\ExpressionInterface;
use InvalidArgumentException;

/**
 * Class ExpressionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionNodePromoter
{
    /**
     * @var ExpressionNodeTypePromoterInterface[]
     */
    private $typePromoters = [];

    /**
     * Adds a promoter for a new type of expression node type
     *
     * @param ExpressionNodeTypePromoterInterface $typePromoter
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
     * Promotes an ExpressionNodeInterface to an Expression
     *
     * @param ExpressionNodeInterface $expressionNode
     * @return ExpressionInterface
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
