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

use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\Validation\Constraint\AssuredConstraint;
use Combyna\Component\Expression\Validation\Constraint\ResultTypeConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Constraint\CallbackConstraint;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

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
     * {@inheritdoc}
     */
    public function buildBehaviourSpec(BehaviourSpecBuilderInterface $specBuilder)
    {
        $specBuilder->addChildNode($this->leftOperandExpression);
        $specBuilder->addChildNode($this->rightOperandExpression);

        // Ensure the left operand expression can only ever evaluate to a number
        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->leftOperandExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'left operand'
            )
        );

        if (
            !in_array(
                $this->operator,
                [
                    BinaryArithmeticExpression::ADD,
                    BinaryArithmeticExpression::SUBTRACT,
                    BinaryArithmeticExpression::MULTIPLY,
                    BinaryArithmeticExpression::DIVIDE,
                ]
            )
        ) {
            $specBuilder->addConstraint(
                new KnownFailureConstraint(
                    sprintf(
                        'Invalid operator "%s" provided',
                        $this->operator
                    )
                )
            );
        }

        if ($this->operator === BinaryArithmeticExpression::DIVIDE) {
            if ($this->rightOperandExpression instanceof NumberExpressionNode) {
                // Right operand is a number constant - we can just check statically whether it is zero
                if ($this->rightOperandExpression->toNative() === 0) {
                    $specBuilder->addConstraint(
                        new CallbackConstraint(
                            function (ValidationContextInterface $validationContext) {
                                $validationContext->addDivisionByZeroViolation();
                            }
                        )
                    );
                }
                return;
            }

            // Ensure that the divisor is an assured expression, protected by a guard expression
            // to ensure that it will only be evaluated if the divisor is non-zero
            $specBuilder->addConstraint(
                new AssuredConstraint(
                    $this->rightOperandExpression,
                    NonZeroNumberAssurance::TYPE,
                    'divisor (right operand)'
                )
            );

            return;
        }

        $specBuilder->addConstraint(
            new ResultTypeConstraint(
                $this->rightOperandExpression,
                new PresolvedTypeDeterminer(new StaticType(NumberExpression::class)),
                'right operand'
            )
        );
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
    public function getResultTypeDeterminer()
    {
        return new PresolvedTypeDeterminer(new StaticType(NumberExpression::class));
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
}
