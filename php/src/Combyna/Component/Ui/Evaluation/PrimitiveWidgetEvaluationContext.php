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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use Combyna\Component\Ui\Widget\WidgetInterface;
use LogicException;

/**
 * Class PrimitiveWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PrimitiveWidgetEvaluationContext extends AbstractEvaluationContext implements PrimitiveWidgetEvaluationContextInterface
{
    /**
     * @var UiEvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var ViewEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var DefinedWidgetInterface
     */
    private $widget;

    /**
     * @var PrimitiveWidgetDefinition
     */
    private $widgetDefinition;

    /**
     * @var DefinedPrimitiveWidgetStateInterface|null
     */
    private $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param PrimitiveWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedPrimitiveWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        PrimitiveWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedPrimitiveWidgetStateInterface $widgetState = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->widget = $widget;
        $this->widgetDefinition = $widgetDefinition;
        $this->widgetState = $widgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function bubbleEventToParent(
        ProgramStateInterface $programState,
        ProgramInterface $program,
        EventInterface $event,
        WidgetInterface $initialWidget
    ) {
        if ($this->widget === $initialWidget) {
            // We've gone no further up the tree yet - bubble again, as evaluation contexts
            // can span between a compound widget's root widget and the compound defined widget
            return $this->parentContext->bubbleEventToParent(
                $programState,
                $program,
                $event,
                $initialWidget
            );
        }

        return $this->widget->dispatchEvent(
            $programState,
            $program,
            $event,
            $this
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createSubAssuredContext(StaticBagInterface $assuredStaticBag)
    {
        return $this->evaluationContextFactory->createAssuredContext($this, $assuredStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubExpressionContext(ExpressionInterface $expression)
    {
        return $this->evaluationContextFactory->createExpressionContext($this, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag)
    {
        return $this->evaluationContextFactory->createScopeContext($this, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubStoreContext(UiStoreStateInterface $storeState)
    {
        return $this->evaluationContextFactory->createWidgetStoreEvaluationContext($this, $storeState);
    }

    /**
     * {@inheritdoc}
     */
    public function getCaptureLeafwise($captureName)
    {
        if ($this->widget->getCaptureExpressionBag()->hasExpression($captureName)) {
            $definitionSubEvaluationContext = $this->evaluationContextFactory
                ->createPrimitiveWidgetDefinitionEvaluationContext(
                    $this,
                    $this->widgetDefinition,
                    $this->widget,
                    $this->widgetState
                );

            // This widget sets the capture - evaluate and return. Evaluate in the context
            // of the widget definition, so that the expression has access to widget values
            return $this->widget->getCaptureExpressionBag()
                ->getExpression($captureName)
                ->toStatic($definitionSubEvaluationContext);
        }

        foreach ($this->widget->getChildWidgets() as $childWidget) {
            // Fetch the child widget state if it has been created already - during the initial state tree build,
            // child widgets' states are created and added to the parent widget's state in sequence, which works well
            // for backward-references (eg. of captures) as the referenced widget's state will already exist.
            // For forward-references, a transient state will need to be created just for evaluating the initial
            // state (eg. to evaluate any expression that a capture-set uses), which will then be discarded
            // and recreated when that child is reached in the sequence during initial state tree creation
            $childWidgetState = $this->widgetState ?
                $this->widgetState->getChildStates()[$childWidget->getName()] :
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

    /**
     * {@inheritdoc}
     */
    public function getCaptureRootwise($captureName)
    {
        if (!$this->widget->getCaptureStaticBagModel()->definesStatic($captureName)) {
            // This widget does not define the capture - it should be defined by an ancestor further up
            return $this->parentContext->getCaptureRootwise($captureName);
        }

        // This widget defines the capture - it should be set by a descendant (or itself)
        $captureStatic = $this->getCaptureLeafwise($captureName);

        if ($captureStatic === null) {
            // If this widget defines the capture, a descendant _must_ set it
            throw new LogicException(
                sprintf(
                    'Capture "%s" was not set',
                    $captureName
                )
            );
        }

        return $captureStatic;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return array_merge($this->parentContext->getPath(), [$this->widget->getName()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetState()
    {
        return $this->widgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValue($valueName)
    {
        return $this->widgetDefinition->getWidgetValue($valueName, $this->getPath(), $this);
    }
}
