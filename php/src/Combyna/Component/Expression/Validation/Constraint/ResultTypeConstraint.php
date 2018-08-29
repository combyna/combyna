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
use Combyna\Component\Type\TypeInterface;

/**
 * Class ResultTypeConstraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ResultTypeConstraint implements ExpressionValidationConstraintInterface
{
    /**
     * @var TypeInterface
     */
    private $allowedType;

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
     * @param TypeInterface $allowedType
     * @param string $contextDescription
     */
    public function __construct(
        ExpressionNodeInterface $expressionNode,
        TypeInterface $allowedType,
        $contextDescription
    ) {
        $this->allowedType = $allowedType;
        $this->contextDescription = $contextDescription;
        $this->expressionNode = $expressionNode;
    }

    /**
     * Fetches the Type that the result is allowed to be
     *
     * @return TypeInterface
     */
    public function getAllowedType()
    {
        return $this->allowedType;
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
