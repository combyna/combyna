<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Evaluation;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\WidgetInterface;

/**
 * Class WidgetEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetEvaluationContext implements WidgetEvaluationContextInterface
{
    /**
     * @var UiEvaluationContextFactoryInterface
     */
    private $evaluationContextFactory;

    /**
     * @var ViewEvaluationContextInterface
     */
    private $parentContext;

    /**
     * @var WidgetInterface
     */
    private $widget;

    /**
     * @param UiEvaluationContextFactoryInterface $evaluationContextFactory
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetInterface $widget
     */
    public function __construct(
        UiEvaluationContextFactoryInterface $evaluationContextFactory,
        ViewEvaluationContextInterface $parentContext,
        WidgetInterface $widget
    ) {
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->parentContext = $parentContext;
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
    public function getAssuredStatic($assuredStaticName)
    {
        return $this->parentContext->getAssuredStatic($assuredStaticName);
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
    public function createSubWidgetEvaluationContext(WidgetInterface $widget)
    {
        return $this->evaluationContextFactory->createWidgetEvaluationContext($this, $widget);
    }
}
