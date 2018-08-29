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

/**
 * Class AssuredConstraint
 *
 * Checks that an expression is an AssuredExpression, and that the assured static
 * it refers to has the specified constraint
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredConstraint implements ExpressionValidationConstraintInterface
{
    /**
     * @var string
     */
    private $constraint;

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
     * @param string $constraint
     * @param string $contextDescription A description of the context: eg. 'left operand'
     */
    public function __construct(
        ExpressionNodeInterface $expressionNode,
        $constraint,
        $contextDescription
    ) {
        $this->constraint = $constraint;
        $this->contextDescription = $contextDescription;
        $this->expressionNode = $expressionNode;
    }

    /**
     * Fetches the assurance constraint (one of the AssuranceInterface::* constants)
     *
     * @return string
     */
    public function getConstraint()
    {
        return $this->constraint;
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
