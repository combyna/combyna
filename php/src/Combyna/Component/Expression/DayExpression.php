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

/**
 * Class DayExpression
 *
 * Evaluates to a date that represents a single but entire day
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DayExpression extends AbstractExpression
{
    const TYPE = 'date';

    /**
     * @var ExpressionInterface
     */
    private $dayExpression;

    /**
     * @var ExpressionInterface
     */
    private $monthExpression;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var ExpressionInterface
     */
    private $yearExpression;

    /**
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param ExpressionInterface $yearExpression
     * @param ExpressionInterface $monthExpression
     * @param ExpressionInterface $dayExpression
     */
    public function __construct(
        StaticExpressionFactoryInterface $staticExpressionFactory,
        ExpressionInterface $yearExpression,
        ExpressionInterface $monthExpression,
        ExpressionInterface $dayExpression
    ) {
        $this->dayExpression = $dayExpression;
        $this->monthExpression = $monthExpression;
        $this->staticExpressionFactory = $staticExpressionFactory;
        $this->yearExpression = $yearExpression;
    }

    /**
     * {@inheritdoc}
     */
    public function toStatic(EvaluationContextInterface $evaluationContext)
    {
        $subEvaluationContext = $evaluationContext->createSubExpressionContext($this);

        $yearStatic = $this->yearExpression->toStatic($subEvaluationContext);
        $monthStatic = $this->monthExpression->toStatic($subEvaluationContext);
        $dayStatic = $this->dayExpression->toStatic($subEvaluationContext);
        
        return $this->staticExpressionFactory->createStaticDayExpression(
            $yearStatic->toNative(),
            $monthStatic->toNative(),
            $dayStatic->toNative()
        );
    }
}
