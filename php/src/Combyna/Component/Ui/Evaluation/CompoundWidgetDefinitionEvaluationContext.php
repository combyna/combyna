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

use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Class CompoundWidgetDefinitionEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetDefinitionEvaluationContext extends AbstractEvaluationContext implements CompoundWidgetDefinitionEvaluationContextInterface
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
     * @var WidgetDefinitionInterface
     */
    private $widgetDefinition;

    /**
     * @var DefinedCompoundWidgetStateInterface|null
     */
    private $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetDefinitionInterface $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedCompoundWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        WidgetDefinitionInterface $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedCompoundWidgetStateInterface $widgetState = null
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
    public function createSubStoreContext(UiStoreStateInterface $storeState)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getChildWidget($childName)
    {
        return $this->widget->getChildWidget($childName);
    }

    /**
     * {@inheritdoc}
     */
    public function getCompoundWidgetDefinitionContext()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        // Parent context will always be the compound widget, so no need to add anything extra
        // to the path for this definition sub-context
        return $this->parentContext->getPath();
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
    public function getWidgetAttribute($attributeName)
    {
        return $this->widget->getAttribute($attributeName, $this->parentContext);
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
