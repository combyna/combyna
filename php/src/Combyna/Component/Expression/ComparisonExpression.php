<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use InvalidArgumentException;

/**
 * Class ComparisonExpression
 *
 * Compares two expressions with the specified operator and returns the result
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpression extends AbstractExpression
{
    const TYPE = 'comparison';

    const EQUAL = '=';
    const EQUAL_CASE_INSENSITIVE = '~=';

    const UNEQUAL = '<>';
    const UNEQUAL_CASE_INSENSITIVE = '~<>';

    const LESS_THAN = '<';
    const GREATER_THAN = '>';

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionInterface
     */
    private $leftOperandExpression;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var ExpressionInterface
     */
    private $rightOperandExpression;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionInterface $leftOperandExpression
     * @param string $operator
     * @param ExpressionInterface $rightOperandExpression
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionInterface $leftOperandExpression,
        $operator,
        ExpressionInterface $rightOperandExpression
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->leftOperandExpression = $leftOperandExpression;
        $this->operator = $operator;
        $this->rightOperandExpression = $rightOperandExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $leftOperandStatic = $this->leftOperandExpression->toStatic($subEvaluationContext);
        $rightOperandStatic = $this->rightOperandExpression->toStatic($subEvaluationContext);

        switch ($this->operator) {
            case self::EQUAL:
                // For text expressions, this will be a case-sensitive comparison
                return $this->expressionFactory->createBooleanExpression(
                    $leftOperandStatic->toNative() === $rightOperandStatic->toNative()
                );
            case self::EQUAL_CASE_INSENSITIVE:
                return $this->expressionFactory->createBooleanExpression(
                    strtolower($leftOperandStatic->toNative()) === strtolower($rightOperandStatic->toNative())
                );
            case self::GREATER_THAN:
                return $this->expressionFactory->createBooleanExpression(
                    $leftOperandStatic->toNative() > $rightOperandStatic->toNative()
                );
            case self::LESS_THAN:
                return $this->expressionFactory->createBooleanExpression(
                    $leftOperandStatic->toNative() < $rightOperandStatic->toNative()
                );
            case self::UNEQUAL:
                // For text expressions, this will be a case-sensitive comparison
                return $this->expressionFactory->createBooleanExpression(
                    $leftOperandStatic->toNative() !== $rightOperandStatic->toNative()
                );
            case self::UNEQUAL_CASE_INSENSITIVE:
                return $this->expressionFactory->createBooleanExpression(
                    strtolower($leftOperandStatic->toNative()) !== strtolower($rightOperandStatic->toNative())
                );
            default:
                throw new InvalidArgumentException(
                    'ComparisonExpression :: Invalid operator "' . $this->operator . '" provided'
                );
        }
    }
}
