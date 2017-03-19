<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use LogicException;

/**
 * Class ConditionalExpression
 *
 * Returns one expression if the condition evaluates to true and the other if false
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalExpression extends AbstractExpression
{
    const TYPE = 'conditional';

    /**
     * @var ExpressionInterface
     */
    private $alternateExpression;

    /**
     * @var ExpressionInterface
     */
    private $conditionExpression;

    /**
     * @var ExpressionInterface
     */
    private $consequentExpression;

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionInterface $conditionExpression
     * @param ExpressionInterface $consequentExpression
     * @param ExpressionInterface $alternateExpression
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionInterface $conditionExpression,
        ExpressionInterface $consequentExpression,
        ExpressionInterface $alternateExpression
    ) {
        $this->alternateExpression = $alternateExpression;
        $this->conditionExpression = $conditionExpression;
        $this->consequentExpression = $consequentExpression;
        $this->expressionFactory = $expressionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);
        $conditionStatic = $this->conditionExpression->toStatic($subEvaluationContext);

        if (!$conditionStatic instanceof BooleanExpression) {
            throw new LogicException(
                'ConditionalExpression :: Condition can only evaluate to a boolean, ' .
                'but got a(n) "' . $conditionStatic->getType() . '"'
            );
        }

        return $conditionStatic->toNative() ?
            $this->consequentExpression->toStatic($subEvaluationContext) :
            $this->alternateExpression->toStatic($subEvaluationContext);
    }
}
