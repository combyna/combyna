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

use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\State\StatePathInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\CoreWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContext;
use Combyna\Component\Ui\View\ViewInterface;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Ui\Widget\CoreWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use LogicException;

/**
 * Class UiEvaluationContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class UiEvaluationContextFactory implements UiEvaluationContextFactoryInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var EvaluationContextFactoryInterface
     */
    private $parentContextFactory;

    /**
     * @var StaticExpressionFactoryInterface
     */
    private $staticExpressionFactory;

    /**
     * @param EvaluationContextFactoryInterface $parentContextFactory
     * @param BagFactoryInterface $bagFactory
     * @param StaticExpressionFactoryInterface $staticExpressionFactory
     */
    public function __construct(
        EvaluationContextFactoryInterface $parentContextFactory,
        BagFactoryInterface $bagFactory,
        StaticExpressionFactoryInterface $staticExpressionFactory
    ) {
        $this->bagFactory = $bagFactory;
        $this->parentContextFactory = $parentContextFactory;
        $this->staticExpressionFactory = $staticExpressionFactory;
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
    public function createCompoundWidgetDefinitionEvaluationContext(
        CompoundWidgetEvaluationContextInterface $parentContext,
        CompoundWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedCompoundWidgetStateInterface $widgetState = null
    ) {
        return new CompoundWidgetDefinitionEvaluationContext(
            $this,
            $parentContext,
            $widgetDefinition,
            $widget,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createCompoundWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        CompoundWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedCompoundWidgetStateInterface $widgetState = null
    ) {
        return new CompoundWidgetEvaluationContext(
            $this,
            $parentContext,
            $widgetDefinition,
            $widget,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createCoreWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        CoreWidgetInterface $widget,
        CoreWidgetStateInterface $widgetState = null
    ) {
        return new CoreWidgetEvaluationContext(
            $this,
            $parentContext,
            $this->bagFactory,
            $this->staticExpressionFactory,
            $widget,
            $widgetState
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
    public function createPrimitiveWidgetDefinitionEvaluationContext(
        PrimitiveWidgetEvaluationContextInterface $parentContext,
        PrimitiveWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedPrimitiveWidgetStateInterface $widgetState = null
    ) {
        return new PrimitiveWidgetDefinitionEvaluationContext(
            $this,
            $parentContext,
            $widgetDefinition,
            $widget,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createPrimitiveWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        PrimitiveWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedPrimitiveWidgetStateInterface $widgetState = null
    ) {
        return new PrimitiveWidgetEvaluationContext(
            $this,
            $parentContext,
            $widgetDefinition,
            $widget,
            $widgetState
        );
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
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment,
        PageViewStateInterface $pageViewState = null
    ) {
        return new RootViewEvaluationContext(
            $this,
            $view,
            $parentContext,
            $environment,
            $pageViewState
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
    public function createSignalContext(
        EvaluationContextInterface $parentContext,
        SignalInterface $signal
    ) {
        return $this->parentContextFactory->createSignalContext(
            $parentContext,
            $signal
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createViewEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag = null
    ) {
        return new ViewEvaluationContext($this, $parentContext, $variableStaticBag);
    }

    /**
     * {@inheritdoc}
     */
    public function createViewStoreEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiStoreStateInterface $viewStoreState
    ) {
        return new ViewStoreEvaluationContext($this, $parentContext, $viewStoreState);
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
            $parentContext,
            $environment,
            $viewState
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
     * Creates a DefinedWidgetEvaluationContext from a DefinedWidgetState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param DefinedWidgetStateInterface $widgetState
     * @param ProgramInterface $program
     * @return DefinedWidgetEvaluationContextInterface
     */
    public function createWidgetEvaluationContextFromDefinedWidgetStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        DefinedWidgetStateInterface $widgetState,
        ProgramInterface $program
    ) {
        /** @var DefinedWidgetInterface $widget */
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        $evaluationContext = $widget->createEvaluationContext($parentContext, $this, $widgetState);

        if (!$evaluationContext instanceof DefinedWidgetEvaluationContextInterface) {
            throw new LogicException(
                sprintf(
                    'Expected a %s, got %s',
                    DefinedWidgetEvaluationContextInterface::class,
                    get_class($evaluationContext)
                )
            );
        }

        return $evaluationContext;
    }

    /**
     * Creates a CoreWidgetEvaluationContext from a WidgetGroupState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param WidgetGroupStateInterface $widgetState
     * @param ProgramInterface $program
     * @return CoreWidgetEvaluationContext
     */
    public function createWidgetEvaluationContextFromWidgetGroupStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        WidgetGroupStateInterface $widgetState,
        ProgramInterface $program
    ) {
        /** @var CoreWidgetInterface $widget */
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        return $this->createCoreWidgetEvaluationContext(
            $parentContext,
            $widget,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getStateTypeToContextFactoryMap()
    {
        return [
            DefinedCompoundWidgetStateInterface::TYPE => [$this, 'createWidgetEvaluationContextFromDefinedWidgetStatePath'],
            DefinedPrimitiveWidgetStateInterface::TYPE => [$this, 'createWidgetEvaluationContextFromDefinedWidgetStatePath'],
            PageViewStateInterface::TYPE => [$this, 'createPageViewEvaluationContextFromPageViewStatePath'],
            WidgetGroupStateInterface::TYPE => [$this, 'createWidgetEvaluationContextFromWidgetGroupStatePath']
        ];
    }
}
