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
 * Class MapExpression
 *
 * Evaluates a list of expressions, mapping their static values to a second list of statics
 * using the result of evaluating a specific mapping expression
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MapExpression extends AbstractExpression
{
    const TYPE = 'map';

    /**
     * @var ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var string|null
     */
    private $indexVariableName;

    /**
     * @var string
     */
    private $itemVariableName;

    /**
     * @var ExpressionInterface
     */
    private $listExpression;

    /**
     * @var ExpressionInterface
     */
    private $mapExpression;

    /**
     * @param ExpressionFactoryInterface $expressionFactory
     * @param ExpressionInterface $listExpression
     * @param string $itemVariableName
     * @param string|null $indexVariableName
     * @param ExpressionInterface $mapExpression
     */
    public function __construct(
        ExpressionFactoryInterface $expressionFactory,
        ExpressionInterface $listExpression,
        $itemVariableName,
        $indexVariableName,
        ExpressionInterface $mapExpression
    ) {
        $this->expressionFactory = $expressionFactory;
        $this->indexVariableName = $indexVariableName;
        $this->itemVariableName = $itemVariableName;
        $this->listExpression = $listExpression;
        $this->mapExpression = $mapExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);
        $listStatic = $this->listExpression->toStatic($subEvaluationContext);

        if (!$listStatic instanceof StaticListExpression) {
            throw new LogicException(
                'MapExpression :: List can only evaluate to a static list ' .
                'expression, but got a(n) "' . $listStatic->getType() . '"'
            );
        }

        return $listStatic->map(
            $this->itemVariableName,
            $this->indexVariableName,
            $this->mapExpression,
            $subEvaluationContext
        );
    }
}
