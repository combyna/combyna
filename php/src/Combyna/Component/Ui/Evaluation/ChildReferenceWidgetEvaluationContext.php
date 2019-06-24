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
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetStateInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;

/**
 * Class ChildReferenceWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ChildReferenceWidgetEvaluationContext extends AbstractCoreWidgetEvaluationContext implements ChildReferenceWidgetEvaluationContextInterface
{
    /**
     * @var ChildReferenceWidgetInterface
     */
    protected $widget;

    /**
     * @var ChildReferenceWidgetStateInterface|null
     */
    protected $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param ChildReferenceWidgetInterface $widget
     * @param ChildReferenceWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        BagFactoryInterface $bagFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        ChildReferenceWidgetInterface $widget,
        ChildReferenceWidgetStateInterface $widgetState = null
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

        $childWidget = $this->getCompoundWidgetDefinitionContext()
            ->getChildWidget($this->widget->getChildName());

        // Fetch the child widget state if it has been created already, otherwise use none
        $childWidgetState = $this->widgetState ?
            $this->widgetState->getChildState($this->widget->getChildName()) :
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

        // No descendants set the capture
        return null;
    }
}
