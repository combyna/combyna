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

use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;

/**
 * Class PrimitiveWidgetDefinitionEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PrimitiveWidgetDefinitionEvaluationContext extends AbstractEvaluationContext implements PrimitiveWidgetDefinitionEvaluationContextInterface
{
    /**
     * @var UiEvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var PrimitiveWidgetEvaluationContextInterface
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
     * @param PrimitiveWidgetEvaluationContextInterface $parentContext
     * @param PrimitiveWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedPrimitiveWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        PrimitiveWidgetEvaluationContextInterface $parentContext,
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
    public function createSubStoreContext(UiStoreStateInterface $storeState)
    {
        throw new \BadMethodCallException('Not implemented');
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
        // Parent context will always be the primitive widget, so no need to add anything extra
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
        return $this->widget->getAttribute($attributeName, $this);
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
        if ($this->widgetState === null) {
            // When creating the initial state, use the default expression
            // defined for the widget value as its initial value
            // rather than calling the value provider
            return $this->widgetDefinition->getDefaultWidgetValue($valueName, $this);
        }

        return $this->widgetDefinition->getWidgetValue($valueName, $this->getPath(), $this);
    }
}
