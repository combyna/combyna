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

use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Type\StaticType;

/**
 * Class BinaryArithmeticExpressionNode
 *
 * Performs an arithmetic calculation with two operands
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionNode extends AbstractExpressionNode
{
    const TYPE = BinaryArithmeticExpression::TYPE;

    /**
     * @var ExpressionNodeInterface
     */
    private $leftOperandExpression;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var ExpressionNodeInterface
     */
    private $rightOperandExpression;

    /**
     * @param ExpressionNodeInterface $leftOperandExpression
     * @param string $operator
     * @param ExpressionNodeInterface $rightOperandExpression
     */
    public function __construct(
        ExpressionNodeInterface $leftOperandExpression,
        $operator,
        ExpressionNodeInterface $rightOperandExpression
    ) {
        $this->leftOperandExpression = $leftOperandExpression;
        $this->operator = $operator;
        $this->rightOperandExpression = $rightOperandExpression;
    }

    /**
     * Fetches the left operand's expression node
     *
     * @return ExpressionNodeInterface
     */
    public function getLeftOperandExpression()
    {
        return $this->leftOperandExpression;
    }

    /**
     * Fetches the operator for the expression
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(NumberExpression::class);
    }

    /**
     * Fetches the right operand's expression node
     *
     * @return ExpressionNodeInterface
     */
    public function getRightOperandExpression()
    {
        return $this->rightOperandExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubActNodeContext($this);

        $this->leftOperandExpression->validate($subValidationContext);
        $this->rightOperandExpression->validate($subValidationContext);

        // Ensure the left operand expression can only ever evaluate to a number
        $subValidationContext->assertResultType(
            $this->leftOperandExpression,
            new StaticType(NumberExpression::class),
            'left operand'
        );

        if ($this->operator === BinaryArithmeticExpression::DIVIDE) {
            if ($this->rightOperandExpression instanceof NumberExpressionNode) {
                // Right operand is a number constant - we can just check statically whether it is zero
                if ($this->rightOperandExpression->toNative() === 0) {
                    $subValidationContext->addDivisionByZeroViolation();
                }

                return;
            }

            // Ensure that the divisor is an assured expression, protected by a guard expression
            // to ensure that it will only be evaluated if the divisor is non-zero
            $subValidationContext->assertAssured(
                $this->rightOperandExpression,
                AssuranceInterface::NON_ZERO_NUMBER,
                'divisor (right operand)'
            );

            return;
        }

        $subValidationContext->assertResultType(
            $this->rightOperandExpression,
            new StaticType(NumberExpression::class),
            'right operand'
        );
    }
}
