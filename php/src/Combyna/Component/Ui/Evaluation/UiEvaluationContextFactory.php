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
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Ui\ViewInterface;
use Combyna\Component\Ui\WidgetInterface;

/**
 * Class UiEvaluationContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiEvaluationContextFactory implements UiEvaluationContextFactoryInterface
{
    /**
     * @var EvaluationContextFactoryInterface
     */
    private $parentContextFactory;

    /**
     * @param EvaluationContextFactoryInterface $parentContextFactory
     */
    public function __construct(EvaluationContextFactoryInterface $parentContextFactory)
    {
        $this->parentContextFactory = $parentContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createAssuredContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $assuredStaticBag
    ) {
        return $this->parentContextFactory->createAssuredContext(
            $parentContext,
            $assuredStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createExpressionContext(
        EvaluationContextInterface $parentContext,
        ExpressionInterface $expression
    ) {
        return $this->parentContextFactory->createExpressionContext($parentContext, $expression);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootContext(EnvironmentInterface $environment)
    {
        return $this->parentContextFactory->createRootContext($environment);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootViewEvaluationContext(
        ViewInterface $view,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment
    ) {
        return new RootViewEvaluationContext($this, $view, $viewAttributeStaticBag, $parentContext, $environment);
    }

    /**
     * {@inheritdoc}
     */
    public function createScopeContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    ) {
        return $this->parentContextFactory->createScopeContext(
            $parentContext,
            $variableStaticBag
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createViewEvaluationContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    ) {
        return new ViewEvaluationContext($this, $parentContext, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        WidgetInterface $widget
    ) {
        return new WidgetEvaluationContext($this, $parentContext, $widget);
    }
}
