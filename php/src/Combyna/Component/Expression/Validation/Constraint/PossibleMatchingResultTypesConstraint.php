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

use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\TypeInterface;

/**
 * Class PossibleMatchingResultTypesConstraint
 *
 * Checks that both of the specified expressions can only ever evaluate to match
 * one of the provided static types together. If the expressions are only able to evaluate
 * to a static type that doesn't match, then a validation violation will be logged
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PossibleMatchingResultTypesConstraint implements ExpressionValidationConstraintInterface
{
    /**
     * @var TypeInterface[]
     */
    private $allowedMatchingResultTypes;

    /**
     * @var string
     */
    private $leftOperandContextDescription;

    /**
     * @var ExpressionNodeInterface
     */
    private $leftOperandExpressionNode;

    /**
     * @var string
     */
    private $rightOperandContextDescription;

    /**
     * @var ExpressionNodeInterface
     */
    private $rightOperandExpressionNode;

    /**
     * @param ExpressionNodeInterface $leftOperandExpressionNode
     * @param string $leftOperandContextDescription
     * @param ExpressionNodeInterface $rightOperandExpressionNode
     * @param string $rightOperandContextDescription
     * @param TypeInterface[] $allowedMatchingResultTypes
     */
    public function __construct(
        ExpressionNodeInterface $leftOperandExpressionNode,
        $leftOperandContextDescription,
        ExpressionNodeInterface $rightOperandExpressionNode,
        $rightOperandContextDescription,
        array $allowedMatchingResultTypes
    ) {
        $this->allowedMatchingResultTypes = $allowedMatchingResultTypes;
        $this->leftOperandContextDescription = $leftOperandContextDescription;
        $this->leftOperandExpressionNode = $leftOperandExpressionNode;
        $this->rightOperandContextDescription = $rightOperandContextDescription;
        $this->rightOperandExpressionNode = $rightOperandExpressionNode;
    }

    /**
     * Fetches the result types that are allowed
     *
     * @return TypeInterface[]
     */
    public function getAllowedMatchingResultTypes()
    {
        return $this->allowedMatchingResultTypes;
    }

    /**
     * Fetches the description of the left operand's context
     *
     * @return string
     */
    public function getLeftOperandContextDescription()
    {
        return $this->leftOperandContextDescription;
    }

    /**
     * Fetches the left operand's expression node
     *
     * @return ExpressionNodeInterface
     */
    public function getLeftOperandExpressionNode()
    {
        return $this->leftOperandExpressionNode;
    }

    /**
     * Fetches the description of the right operand's context
     *
     * @return string
     */
    public function getRightOperandContextDescription()
    {
        return $this->rightOperandContextDescription;
    }

    /**
     * Fetches the right operand's expression node
     *
     * @return ExpressionNodeInterface
     */
    public function getRightOperandExpressionNode()
    {
        return $this->rightOperandExpressionNode;
    }

    /**
     * {@inheritdoc}
     */
    public function makesQuery(QuerySpecifierInterface $querySpecifier)
    {
        return false;
    }
}
