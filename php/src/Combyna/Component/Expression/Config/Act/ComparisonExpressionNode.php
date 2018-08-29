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
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Expression\Validation\Constraint\PossibleMatchingResultTypesConstraint;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Constraint\KnownFailureConstraint;
use Combyna\Component\Validator\Type\PresolvedTypeDeterminer;

/**
 * Class ComparisonExpressionNode
 *
 * Compares two expressions with the specified operator and returns the result.
 * For text expressions, two additional operators are provided for case-insensitive comparisons
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpressionNode extends AbstractExpressionNode
{
    const TYPE = ComparisonExpression::TYPE;

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

        switch ($this->operator) {
            case ComparisonExpression::EQUAL:
                // For text expressions, this will be a case-sensitive comparison
                $specBuilder->addConstraint(
                    new PossibleMatchingResultTypesConstraint(
                        $this->leftOperandExpression,
                        'left operand',
                        $this->rightOperandExpression,
                        'right operand',
                        [
                            new StaticType(BooleanExpression::class),
                            new StaticType(NumberExpression::class),
                            new StaticType(TextExpression::class)
                        ]
                    )
                );
                break;
            case ComparisonExpression::EQUAL_CASE_INSENSITIVE:
                // Case-insensitive comparison - only makes sense for text expressions,
                // so only text expressions may use it
                $specBuilder->addConstraint(
                    new PossibleMatchingResultTypesConstraint(
                        $this->leftOperandExpression,
                        'left operand',
                        $this->rightOperandExpression,
                        'right operand',
                        [
                            new StaticType(TextExpression::class)
                        ]
                    )
                );
                break;
            case ComparisonExpression::GREATER_THAN:
                $specBuilder->addConstraint(
                    new PossibleMatchingResultTypesConstraint(
                        $this->leftOperandExpression,
                        'left operand',
                        $this->rightOperandExpression,
                        'right operand',
                        [
                            new StaticType(NumberExpression::class)
                        ]
                    )
                );
                break;
            case ComparisonExpression::LESS_THAN:
                $specBuilder->addConstraint(
                    new PossibleMatchingResultTypesConstraint(
                        $this->leftOperandExpression,
                        'left operand',
                        $this->rightOperandExpression,
                        'right operand',
                        [
                            new StaticType(NumberExpression::class)
                        ]
                    )
                );
                break;
            case ComparisonExpression::UNEQUAL:
                // For text expressions, this will be a case-sensitive comparison
                $specBuilder->addConstraint(
                    new PossibleMatchingResultTypesConstraint(
                        $this->leftOperandExpression,
                        'left operand',
                        $this->rightOperandExpression,
                        'right operand',
                        [
                            new StaticType(BooleanExpression::class),
                            new StaticType(NumberExpression::class),
                            new StaticType(TextExpression::class)
                        ]
                    )
                );
                break;
            case ComparisonExpression::UNEQUAL_CASE_INSENSITIVE:
                // Case-insensitive comparison - only makes sense for text expressions,
                // so only text expressions may use it
                $specBuilder->addConstraint(
                    new PossibleMatchingResultTypesConstraint(
                        $this->leftOperandExpression,
                        'left operand',
                        $this->rightOperandExpression,
                        'right operand',
                        [
                            new StaticType(TextExpression::class)
                        ]
                    )
                );
                break;
            default:
                $specBuilder->addConstraint(
                    new KnownFailureConstraint(
                        sprintf(
                            'Invalid operator "%s" provided',
                            $this->operator
                        )
                    )
                );
        }
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
        return new PresolvedTypeDeterminer(new StaticType(BooleanExpression::class));
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
