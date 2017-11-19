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
use LogicException;

/**
 * Class ConcatenationExpression
 *
 * Concatenates a series of text or number values to form a string
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpression extends AbstractExpression
{
    const TYPE = 'concatenation';

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ExpressionInterface|null
     */
    private $glueExpression;

    /**
     * @var ExpressionInterface
     */
    private $operandListExpression;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionInterface $operandListExpression
     * @param ExpressionInterface|null $glueExpression
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionInterface $operandListExpression,
        ExpressionInterface $glueExpression = null
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->glueExpression = $glueExpression;
        $this->operandListExpression = $operandListExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $operandListStatic = $this->operandListExpression->toStatic($subEvaluationContext);

        if (!$operandListStatic instanceof StaticListExpression) {
            throw new LogicException(
                'ConcatenationExpression :: List can only evaluate to a static-list ' .
                'or error expression, but got a(n) "' . $operandListStatic->getType() . '"'
            );
        }

        $glueStatic = $this->glueExpression ?
            $this->glueExpression->toStatic($subEvaluationContext) :
            null;

        // NumberExpressions' floats or integers should be coerced to string at this point
        return $operandListStatic->concatenate($glueStatic ? $glueStatic->toNative() : '');
    }
}
