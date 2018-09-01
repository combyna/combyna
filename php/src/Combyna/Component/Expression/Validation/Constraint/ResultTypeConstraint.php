<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Validation\Constraint;

use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Validator\Type\TypeDeterminerInterface;

/**
 * Class ResultTypeConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResultTypeConstraint implements ExpressionValidationConstraintInterface
{
    /**
     * @var TypeDeterminerInterface
     */
    private $allowedTypeDeterminer;

    /**
     * @var string
     */
    private $contextDescription;

    /**
     * @var ExpressionNodeInterface
     */
    private $expressionNode;

    /**
     * @param ExpressionNodeInterface $expressionNode
     * @param TypeDeterminerInterface $allowedTypeDeterminer
     * @param string $contextDescription
     */
    public function __construct(
        ExpressionNodeInterface $expressionNode,
        TypeDeterminerInterface $allowedTypeDeterminer,
        $contextDescription
    ) {
        $this->allowedTypeDeterminer = $allowedTypeDeterminer;
        $this->contextDescription = $contextDescription;
        $this->expressionNode = $expressionNode;
    }

    /**
     * Fetches the Type that the result is allowed to be
     *
     * @return TypeDeterminerInterface
     */
    public function getAllowedTypeDeterminer()
    {
        return $this->allowedTypeDeterminer;
    }

    /**
     * Fetches a description of the value being constrained
     *
     * @return string
     */
    public function getContextDescription()
    {
        return $this->contextDescription;
    }

    /**
     * Fetches the expression node that the constraint is being applied to
     *
     * @return ExpressionNodeInterface
     */
    public function getExpressionNode()
    {
        return $this->expressionNode;
    }
}
