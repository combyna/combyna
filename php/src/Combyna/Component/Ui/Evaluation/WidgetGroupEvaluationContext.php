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
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;

/**
 * Class WidgetGroupEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroupEvaluationContext extends AbstractCoreWidgetEvaluationContext implements WidgetGroupEvaluationContextInterface
{
    /**
     * @var WidgetGroupInterface
     */
    protected $widget;

    /**
     * @var WidgetGroupStateInterface|null
     */
    protected $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param WidgetGroupInterface $widget
     * @param WidgetGroupStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        BagFactoryInterface $bagFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        WidgetGroupInterface $widget,
        WidgetGroupStateInterface $widgetState = null
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

        foreach ($this->widget->getChildWidgets() as $childWidget) {
            // Fetch the child widget state if it has been created already, otherwise use none
            $childWidgetState = $this->widgetState ?
                $this->widgetState->getChildState($childWidget->getName()) :
                null;

            $childWidgetEvaluationContext = $childWidget
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
