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
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\State\StatePathInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContext;
use Combyna\Component\Ui\View\ViewInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

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
    public function createEventContext(
        EvaluationContextInterface $parentContext,
        EventInterface $event
    ) {
        return $this->parentContextFactory->createEventContext($parentContext, $event);
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
        ViewStoreStateInterface $viewStoreState,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment
    ) {
        return new RootViewEvaluationContext(
            $this,
            $view,
            $viewStoreState,
            $viewAttributeStaticBag,
            $parentContext,
            $environment
        );
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
        UiEvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag = null
    ) {
        return new ViewEvaluationContext($this, $parentContext, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStoreEvaluationContext(
        EvaluationContextInterface $parentContext,
        UiStoreStateInterface $viewStoreState
    ) {
        return new ViewStoreEvaluationContext($this, $parentContext, $viewStoreState);
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

    /**
     * {@inheritdoc}
     */
    public function createPageViewEvaluationContextFromPageViewState(
        EvaluationContextInterface $parentContext,
        PageViewStateInterface $viewState,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        $view = $program->getPageViewByName($viewState->getViewName());

        $rootEvaluationContext = new RootViewEvaluationContext(
            $this,
            $view,
            $viewState->getStoreState(),
            $viewState->getAttributeStaticBag(),
            $parentContext,
            $environment
        );

        return new ViewEvaluationContext($this, $rootEvaluationContext);
    }

    /**
     * Creates a ViewEvaluationContext from a PageViewState
     *
     * @param EvaluationContextInterface $parentContext
     * @param StatePathInterface $viewStatePath
     * @param PageViewStateInterface $viewState
     * @param ProgramInterface $program
     * @param EnvironmentInterface $environment
     * @return ViewEvaluationContext
     */
    public function createPageViewEvaluationContextFromPageViewStatePath(
        EvaluationContextInterface $parentContext,
        StatePathInterface $viewStatePath,
        PageViewStateInterface $viewState,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        return $this->createPageViewEvaluationContextFromPageViewState(
            $parentContext,
            $viewState,
            $program,
            $environment
        );
    }

    /**
     * Creates a WidgetEvaluationContext from a DefinedWidgetState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param DefinedWidgetStateInterface $widgetState
     * @param ProgramInterface $program
     * @param EnvironmentInterface $environment
     * @return WidgetEvaluationContext
     */
    public function createWidgetEvaluationContextFromDefinedWidgetStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        DefinedWidgetStateInterface $widgetState,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        return new WidgetEvaluationContext($this, $parentContext, $widget/*, $widgetState*/);
    }

    /**
     * Creates a WidgetEvaluationContext from a WidgetGroupState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param DefinedWidgetStateInterface $widgetState
     * @param ProgramInterface $program
     * @return WidgetEvaluationContext
     */
    public function createWidgetEvaluationContextFromWidgetGroupStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        WidgetGroupStateInterface $widgetState,
        ProgramInterface $program,
        EnvironmentInterface $environment
    ) {
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        return new WidgetEvaluationContext($this, $parentContext, $widget/*, $widgetState*/);
    }

    /**
     * {@inheritdoc}
     */
    public function getStateTypeToContextFactoryMap()
    {
        return [
            DefinedWidgetStateInterface::TYPE => [$this, 'createWidgetEvaluationContextFromDefinedWidgetStatePath'],
            PageViewStateInterface::TYPE => [$this, 'createPageViewEvaluationContextFromPageViewStatePath'],
            WidgetGroupStateInterface::TYPE => [$this, 'createWidgetEvaluationContextFromWidgetGroupStatePath']
        ];
    }
}
