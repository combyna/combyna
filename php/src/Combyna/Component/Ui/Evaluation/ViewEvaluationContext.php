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
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\WidgetInterface;

/**
 * Class ViewEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewEvaluationContext implements ViewEvaluationContextInterface
{
    /**
     * @var UiEvaluationContextFactory
     */
    private $evaluationContextFactory;

    /**
     * @var EvaluationContextInterface
     */
    private $parentContext;

    /**
     * @var StaticBagInterface
     */
    private $variableStaticBag;

    /**
     * @param UiEvaluationContextFactory $evaluationContextFactory
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $variableStaticBag
     */
    public function __construct(
        UiEvaluationContextFactory $evaluationContextFactory,
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    ) {
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->parentContext = $parentContext;
        $this->variableStaticBag = $variableStaticBag;
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
        return $this->evaluationContextFactory->createViewEvaluationContext($this, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubWidgetEvaluationContext(WidgetInterface $widget)
    {
        return $this->evaluationContextFactory->createWidgetEvaluationContext($this, $widget);
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
        if ($this->variableStaticBag->hasStatic($variableName)) {
            return $this->variableStaticBag->getStatic($variableName);
        }

        return $this->parentContext->getVariable($variableName);
    }

    /**
     * {@inheritdoc}
     */
    public function translate($translationKey, array $parameters = [])
    {
        return $this->parentContext->translate($translationKey, $parameters);
    }
}
