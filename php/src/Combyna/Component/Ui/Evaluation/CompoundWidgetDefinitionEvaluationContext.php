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
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;

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
     * @var CompoundWidgetEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var DefinedWidgetInterface
     */
    private $widget;

    /**
     * @var CompoundWidgetDefinition
     */
    private $widgetDefinition;

    /**
     * @var DefinedCompoundWidgetStateInterface|null
     */
    private $widgetState;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param CompoundWidgetEvaluationContextInterface $parentContext
     * @param CompoundWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedCompoundWidgetStateInterface|null $widgetState
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        CompoundWidgetEvaluationContextInterface $parentContext,
        CompoundWidgetDefinition $widgetDefinition,
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
    public function createSubStoreContext(UiStoreStateInterface $storeState)
    {
        throw new \BadMethodCallException('Not implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function getChildOfCurrentCompoundWidget($childName)
    {
        return $this->widget->getChildWidget($childName);
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
        return $this->widgetDefinition->getWidgetValue($valueName, $this);
    }
}
