<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Expression;

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Type\StaticType;
use InvalidArgumentException;

/**
 * Class TextComparisonExpression
 *
 * Compares two texts with the specified operator and returns the result
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextComparisonExpression extends AbstractExpression
{
    const TYPE = 'text-comparison';

    const EQUALS_CASE_INSENSITIVE = '~ =';
    const EQUALS_CASE_SENSITIVE = '=';

    const IS_NOT_EQUAL_TO_CASE_INSENSITIVE = '~ <>';
    const IS_NOT_EQUAL_TO_CASE_SENSITIVE = '<>';

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
        $subEvaluationContext = $evaluationContext->createSubContext($this);

        $leftOperandStatic = $this->leftOperandExpression->toStatic($subEvaluationContext);
        $rightOperandStatic = $this->rightOperandExpression->toStatic($subEvaluationContext);

        switch ($this->operator) {
            case self::EQUALS_CASE_INSENSITIVE:
                return $this->expressionFactory->createBooleanExpression(
                    strtolower($leftOperandStatic->toNative()) === strtolower($rightOperandStatic->toNative())
                );
            case self::EQUALS_CASE_SENSITIVE:
                return $this->expressionFactory->createBooleanExpression(
                    $leftOperandStatic->toNative() === $rightOperandStatic->toNative()
                );
            case self::IS_NOT_EQUAL_TO_CASE_INSENSITIVE:
                return $this->expressionFactory->createBooleanExpression(
                    strtolower($leftOperandStatic->toNative()) !== strtolower($rightOperandStatic->toNative())
                );
            case self::IS_NOT_EQUAL_TO_CASE_SENSITIVE:
                return $this->expressionFactory->createBooleanExpression(
                    $leftOperandStatic->toNative() !== $rightOperandStatic->toNative()
                );
            default:
                throw new InvalidArgumentException(
                    'TextComparisonExpression :: Invalid operator "' . $this->operator . '" provided'
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResultType(ValidationContextInterface $validationContext)
    {
        return new StaticType(BooleanExpression::class);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(ValidationContextInterface $validationContext)
    {
        $subValidationContext = $validationContext->createSubContext($this);

        $this->leftOperandExpression->validate($subValidationContext);
        $this->rightOperandExpression->validate($subValidationContext);

        $subValidationContext->assertOperator($this->operator, [
            self::EQUALS_CASE_INSENSITIVE,
            self::EQUALS_CASE_SENSITIVE,
            self::IS_NOT_EQUAL_TO_CASE_INSENSITIVE,
            self::IS_NOT_EQUAL_TO_CASE_SENSITIVE
        ]);

        // Ensure the left and right operand expressions can only ever evaluate to texts
        $subValidationContext->assertResultType(
            $this->leftOperandExpression,
            new StaticType(TextExpression::class),
            'left operand'
        );
        $subValidationContext->assertResultType(
            $this->rightOperandExpression,
            new StaticType(TextExpression::class),
            'right operand'
        );
    }
}
