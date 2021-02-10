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
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Widget\ConditionalWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\CoreWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;
use Combyna\Component\Ui\Widget\CoreWidgetInterface;
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;
use LogicException;

/**
 * Class CoreWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CoreWidgetEvaluationContext extends AbstractEvaluationContext implements CoreWidgetEvaluationContextInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var ViewEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @var CoreWidgetInterface
     */
    private $widget;

    /**
     * @var CoreWidgetStateInterface|null
     */
    private $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param CoreWidgetInterface $widget
     * @param CoreWidgetStateInterface $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        BagFactoryInterface $bagFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory,
        CoreWidgetInterface $widget,
        CoreWidgetStateInterface $widgetState = null
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->bagFactory = $bagFactory;
        $this->staticExpressionFactory = $staticExpressionFactory;
        $this->widget = $widget;
        $this->widgetState = $widgetState;
    }

    /**
     * {@inheritdoc}
     */
    public function createSubScopeContext(StaticBagInterface $variableStaticBag)
    {
        return $this->evaluationContextFactory->createViewEvaluationContext($this, $variableStaticBag);
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
            // This widget sets the capture - evaluate and return
            return $this->widget->getCaptureExpressionBag()->getExpression($captureName)->toStatic($this);
        }

        // TODO: Factor these out into separate CoreWidgetEvaluationContexts (Repeater*, WidgetGroup* etc.)
        //       and remove this class
        if ($this->widget instanceof WidgetGroupInterface) {
            $widgetState = $this->widgetState;

            if ($widgetState && !($widgetState instanceof WidgetGroupStateInterface)) {
                throw new LogicException(
                    sprintf(
                        'Expected %s, got %s',
                        WidgetGroupStateInterface::class,
                        get_class($widgetState)
                    )
                );
            }

            foreach ($this->widget->getChildWidgets() as $childWidget) {
                // Fetch the child widget state if it has been created already, otherwise use none
                $childWidgetState = $widgetState ?
                    $widgetState->getChildState($childWidget->getName()) :
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
        } elseif ($this->widget instanceof RepeaterWidgetInterface) {
            // Fetch the repeated widget states if they have been created already,
            // otherwise create a new, transient set just for evaluating this capture
            /** @var RepeaterWidgetStateInterface|null $repeaterState */
            $repeaterState = $this->widgetState;

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
                ) use ($captureName, $repeatedWidget, $repeaterState) {
                    // Fetch the repeated widget state if it has been created already, otherwise use none
                    $repeatedWidgetState = $repeaterState && $index < count($repeaterState->getRepeatedWidgetStates()) ?
                        $repeaterState->getRepeatedWidgetStates()[$index] :
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
        } elseif ($this->widget instanceof ConditionalWidgetInterface) {
            $widgetState = $this->widgetState;

            if ($widgetState && !($widgetState instanceof ConditionalWidgetStateInterface)) {
                throw new LogicException(
                    sprintf(
                        'Expected %s, got %s',
                        WidgetGroupStateInterface::class,
                        get_class($widgetState)
                    )
                );
            }

            // Evaluate the condition to determine whether the consequent or alternate widget is present
            $conditionStatic = $this->widget->getCondition()->toStatic($this);

            if ($conditionStatic->toNative() === true) {
                // Fetch the consequent widget state if it has been created already, otherwise use none
                $childWidgetState = $widgetState ?
                    $widgetState->getConsequentWidgetState() :
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
                $childWidgetState = $widgetState ?
                    $widgetState->getAlternateWidgetState() :
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
            // No descendant set the capture - use the default value for the capture if defined
            // (if not defined, an exception will be thrown, as validation should have ensured
            // that a capture that is able to not be set always has a default expression defined)
            $captureStatic = $this->widget->getCaptureStaticBagModel()->getDefaultStatic($captureName, $this);
        }

        return $captureStatic;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildOfCurrentCompoundWidget($childName)
    {
        return $this->parentContext->getChildOfCurrentCompoundWidget($childName);
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        // For each instance of a repeated widget (when this context is for a Repeater),
        // the widget name will be constant ("repeated") whereas the state name will be 0...N
        $widgetName = $this->widgetState ?
            $this->widgetState->getStateName() :
            $this->widget->getName();

        return array_merge($this->parentContext->getPath(), [$widgetName]);
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
}
