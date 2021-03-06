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
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Widget\CoreWidgetStateInterface;
use Combyna\Component\Ui\Widget\CoreWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Class AbstractCoreWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractCoreWidgetEvaluationContext extends AbstractEvaluationContext implements CoreWidgetEvaluationContextInterface
{
    /**
     * @var BagFactoryInterface
     */
    protected $bagFactory;

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
    protected $staticExpressionFactory;

    /**
     * @var CoreWidgetInterface
     */
    protected $widget;

    /**
     * @var CoreWidgetStateInterface|null
     */
    protected $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     * @param CoreWidgetInterface $widget
     * @param CoreWidgetStateInterface|null $widgetState
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
    public function getCaptureRootwise($captureName)
    {
        if (!$this->widget->getCaptureStaticBagModel()->definesStatic($captureName)) {
            // This widget does not define the capture - it should be defined by an ancestor further up
            return $this->parentContext->getCaptureRootwise($captureName);
        }

        // NB: If no descendant set the capture - use the default value for the capture if defined
        // (if not defined, an exception will be thrown, as validation should have ensured
        // that a capture that is able to not be set always has a default expression defined)
        return $this->widget->getCaptureStaticBagModel()
            ->coerceStatic(
                $captureName,
                $this,
                $this->widget->getCaptureExpressionBag(),
                $this->getCaptureLeafwise($captureName)
            );
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
