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

use Combyna\Component\Bag\ExpressionBagInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\AbstractEvaluationContext;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;

/**
 * Class CompoundWidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class CompoundWidgetEvaluationContext extends AbstractEvaluationContext implements CompoundWidgetEvaluationContextInterface
{
    /**
     * @var StaticBagInterface
     */
    private $attributeStaticBag;

    /**
     * @var UiEvaluationContextFactoryInterface
     */
    protected $evaluationContextFactory;

    /**
     * @var ViewEvaluationContextInterface
     */
    protected $parentContext;

    /**
     * @var ExpressionBagInterface
     */
    private $valueExpressionBag;

    /**
     * @var DefinedWidgetInterface
     */
    private $widget;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param ExpressionBagInterface $valueExpressionBag
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        ExpressionBagInterface $valueExpressionBag
    ) {
        parent::__construct($evaluationContextFactory, $parentContext);

        $this->attributeStaticBag = $attributeStaticBag;
        $this->valueExpressionBag = $valueExpressionBag;
        $this->widget = $widget;
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
    public function getChildWidget($childName)
    {
        return $this->widget->getChildWidget($childName);
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
    public function getWidgetAttribute($attributeName)
    {
        return $this->widget->getAttribute($attributeName, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetValue($valueName)
    {
        return $this->valueExpressionBag->getExpression($valueName)->toStatic($this);
    }
}
