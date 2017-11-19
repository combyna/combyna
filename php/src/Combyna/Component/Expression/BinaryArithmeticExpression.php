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
use LogicException;

/**
 * Class BinaryArithmeticExpression
 *
 * Performs an arithmetic calculation with two operands
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpression extends AbstractExpression
{
    const TYPE = 'binary-arithmetic';

    const ADD = '+';
    const SUBTRACT = '-';
    const MULTIPLY = '*';
    const DIVIDE = '/';

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
            case self::ADD:
                $result = $leftOperandStatic->toNative() + $rightOperandStatic->toNative();
                break;
            case self::SUBTRACT:
                $result = $leftOperandStatic->toNative() - $rightOperandStatic->toNative();
                break;
            case self::MULTIPLY:
                $result = $leftOperandStatic->toNative() * $rightOperandStatic->toNative();
                break;
            case self::DIVIDE:
                if ($rightOperandStatic->toNative() === 0) {
                    // Sanity check - this operand should only be provided by an AssuredExpression
                    // (if complex, unless a simple NumberExpression) that ensures it is not zero
                    throw new LogicException('Divide by zero - divisor operand should have been assured');
                }

                $result = $leftOperandStatic->toNative() / $rightOperandStatic->toNative();
                break;
            default:
                throw new InvalidArgumentException(
                    'BinaryArithmeticExpression :: Invalid operator "' . $this->operator . '" provided'
                );
        }

        return $this->expressionFactory->createNumberExpression($result);
    }
}
