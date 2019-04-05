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
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;

/**
 * Class RepeaterWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetEvaluationContext extends AbstractCoreWidgetEvaluationContext implements RepeaterWidgetEvaluationContextInterface
{
    /**
     * @var RepeaterWidgetInterface
     */
    protected $widget;

    /**
     * @var RepeaterWidgetStateInterface|null
     */
    protected $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param RepeaterWidgetInterface $widget
     * @param RepeaterWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        BagFactoryInterface $bagFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        RepeaterWidgetInterface $widget,
        RepeaterWidgetStateInterface $widgetState = null
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

        // Fetch the repeated widget states if they have been created already,
        // otherwise create a new, transient set just for evaluating this capture

        $repeatedWidget = $this->widget->getRepeatedWidget();

        if (!$repeatedWidget->descendantsSetCaptureInclusive($captureName)) {
            // Neither the repeated widget, nor a descendant of it, defines the capture -
            // no need to recurse into each repeated instance to check
            return null;
        }

        /** @var StaticInterface[] $captureStatics */
        $captureStatics = $this->widget->mapItemStaticList(
            function (
                ViewEvaluationContextInterface $itemWidgetEvaluationContext,
                StaticInterface $static,
                $index
            ) use ($captureName, $repeatedWidget) {
                // Fetch the repeated widget state if it has been created already, otherwise use none
                $repeatedWidgetState = $this->widgetState && $index < count($this->widgetState->getRepeatedWidgetStates()) ?
                    $this->widgetState->getRepeatedWidgetStates()[$index] :
                    null;

                $repeatedWidgetEvaluationContext = $repeatedWidget
                    ->createEvaluationContext(
                        $itemWidgetEvaluationContext,
                        $this->evaluationContextFactory,
                        $repeatedWidgetState
                    );

                return $repeatedWidgetEvaluationContext->getCaptureLeafwise($captureName);
            },
            $this
        );

        return $this->staticExpressionFactory->createStaticListExpression(
            $this->bagFactory->createStaticList(
                $captureStatics
            )
        );
    }
}
