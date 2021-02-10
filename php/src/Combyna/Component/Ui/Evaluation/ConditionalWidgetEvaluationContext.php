<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Ui\State\Widget\ConditionalWidgetStateInterface;
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;

/**
 * Class ConditionalWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidgetEvaluationContext extends AbstractCoreWidgetEvaluationContext implements ConditionalWidgetEvaluationContextInterface
{
    /**
     * @var ConditionalWidgetInterface
     */
    protected $widget;

    /**
     * @var ConditionalWidgetStateInterface|null
     */
    protected $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param ConditionalWidgetInterface $widget
     * @param ConditionalWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        BagFactoryInterface $bagFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        ConditionalWidgetInterface $widget,
        ConditionalWidgetStateInterface $widgetState = null
    ) {
        parent::__construct(
            $evaluationContextFactory,
            $parentContext,
            $bagFactory,
            $staticExpressionFactory,
            $widget,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureLeafwise($captureName)
    {
        if ($this->widget->getCaptureExpressionBag()->hasExpression($captureName)) {
            // This widget sets the capture - evaluate and return
            return $this->widget->getCaptureExpressionBag()->getExpression($captureName)->toStatic($this);
        }

        // Evaluate the condition to determine whether the consequent or alternate widget is present
        $conditionStatic = $this->widget->getCondition()->toStatic($this);

        if ($conditionStatic->toNative() === true) {
            // Fetch the consequent widget state if it has been created already, otherwise use none
            $childWidgetState = $this->widgetState ?
                $this->widgetState->getConsequentWidgetState() :
                null;

            $childWidgetEvaluationContext = $this->widget->getConsequentWidget()
                ->createEvaluationContext(
                    $this,
                    $this->evaluationContextFactory,
                    $childWidgetState
                );

            $captureStatic = $childWidgetEvaluationContext->getCaptureLeafwise($captureName);

            if ($captureStatic !== null) {
                return $captureStatic;
            }
        } elseif ($this->widget->getAlternateWidget() !== null) {
            // Otherwise try the alternate widget, if it is defined

            // Fetch the alternate widget state if it has been created already, otherwise use none
            $childWidgetState = $this->widgetState ?
                $this->widgetState->getAlternateWidgetState() :
                null;

            $childWidgetEvaluationContext = $this->widget->getAlternateWidget()
                ->createEvaluationContext(
                    $this,
                    $this->evaluationContextFactory,
                    $childWidgetState
                );

            $captureStatic = $childWidgetEvaluationContext->getCaptureLeafwise($captureName);

            if ($captureStatic !== null) {
                return $captureStatic;
            }
        }

        // No descendants set the capture
        return null;
    }
}
