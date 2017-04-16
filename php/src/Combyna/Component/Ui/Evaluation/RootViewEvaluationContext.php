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
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\ViewInterface;
use Combyna\Component\Ui\WidgetInterface;

/**
 * Class RootViewEvaluationContext
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RootViewEvaluationContext implements ViewEvaluationContextInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var UiEvaluationContextFactory
     */
    private $evaluationContextFactory;

    /**
     * @var EvaluationContextInterface
     */
    private $parentContext;

    /**
     * @var ViewInterface
     */
    private $view;

    /**
     * @var StaticBagInterface|null
     */
    private $viewAttributeStaticBag;

    /**
     * @param UiEvaluationContextFactory $evaluationContextFactory
     * @param ViewInterface $view
     * @param StaticBagInterface $viewAttributeStaticBag
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     */
    public function __construct(
        UiEvaluationContextFactory $evaluationContextFactory,
        ViewInterface $view,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment
    ) {
        $this->environment = $environment;
        $this->evaluationContextFactory = $evaluationContextFactory;
        $this->parentContext = $parentContext;
        $this->view = $view;
        $this->viewAttributeStaticBag = $viewAttributeStaticBag;
    }

    /**
     * {@inheritdoc}
     */
    public function callFunction($libraryName, $functionName, StaticBagInterface $argumentStaticBag)
    {
        return $this->environment->callViewFunction($libraryName, $functionName, $argumentStaticBag);
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
        return $this->evaluationContextFactory->createViewEvaluationContext(
            $this,
            $variableStaticBag
        );
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
