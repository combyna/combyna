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
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\Widget\CoreWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Class CoreWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CoreWidgetEvaluationContext extends AbstractEvaluationContext implements CoreWidgetEvaluationContextInterface
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
     * @var CoreWidgetInterface
     */
    private $widget;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param CoreWidgetInterface $widget
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        CoreWidgetInterface $widget
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->widget = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction($libraryName, $functionName, StaticBagInterface $argumentStaticBag)
    {
        return $this->parentContext->callFunction($libraryName, $functionName, $argumentStaticBag);
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
    public function getAssuredStatic($assuredStaticName)
    {
        return $this->parentContext->getAssuredStatic($assuredStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildWidget($childName)
    {
        return $this->parentContext->getChildWidget($childName);
    }

    /**
     * {@inheritdoc}
     */
    public function getVariable($variableName)
    {
        return $this->parentContext->getVariable($variableName);
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
    public function createSubWidgetEvaluationContext(WidgetInterface $widget)
    {
        return $widget->createEvaluationContext($this, $this->evaluationContextFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($translationKey, array $arguments = [])
    {
        return $this->parentContext->translate($translationKey, $arguments);
    }
}
