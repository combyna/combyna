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
 * Class DateTimeExpression
 *
 * Evaluates to a date and a specific time of day
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DateTimeExpression extends AbstractExpression
{
    const TYPE = 'datetime';

    /**
     * @var ExpressionInterface
     */
    private $dayExpression;

    /**
     * @var ExpressionInterface
     */
    private $hourExpression;

    /**
     * @var ExpressionInterface|null
     */
    private $millisecondExpression;

    /**
     * @var ExpressionInterface
     */
    private $minuteExpression;

    /**
     * @var ExpressionInterface
     */
    private $monthExpression;

    /**
     * @var ExpressionInterface
     */
    private $secondExpression;

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
     * @param ExpressionInterface $hourExpression
     * @param ExpressionInterface $minuteExpression
     * @param ExpressionInterface $secondExpression
     * @param ExpressionInterface|null $millisecondExpression
     */
    public function __construct(
        StaticExpressionFactoryInterface $staticExpressionFactory,
        ExpressionInterface $yearExpression,
        ExpressionInterface $monthExpression,
        ExpressionInterface $dayExpression,
        ExpressionInterface $hourExpression,
        ExpressionInterface $minuteExpression,
        ExpressionInterface $secondExpression,
        ExpressionInterface $millisecondExpression = null
    ) {
        $this->dayExpression = $dayExpression;
        $this->hourExpression = $hourExpression;
        $this->millisecondExpression = $millisecondExpression;
        $this->minuteExpression = $minuteExpression;
        $this->monthExpression = $monthExpression;
        $this->secondExpression = $secondExpression;
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
        $hourStatic = $this->hourExpression->toStatic($subEvaluationContext);
        $minuteStatic = $this->minuteExpression->toStatic($subEvaluationContext);
        $secondStatic = $this->secondExpression->toStatic($subEvaluationContext);
        $millisecondStatic = $this->millisecondExpression ?
            $this->millisecondExpression->toStatic($subEvaluationContext) :
            $this->staticExpressionFactory->createNumberExpression(0);

        return $this->staticExpressionFactory->createStaticDateTimeExpression(
            $yearStatic->toNative(),
            $monthStatic->toNative(),
            $dayStatic->toNative(),
            $hourStatic->toNative(),
            $minuteStatic->toNative(),
            $secondStatic->toNative(),
            $millisecondStatic->toNative()
        );
    }
}
