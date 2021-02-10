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
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\State\StatePathInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\ConditionalWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\TextWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContext;
use Combyna\Component\Ui\View\ViewInterface;
use Combyna\Component\Ui\Widget\ChildReferenceWidgetInterface;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Ui\Widget\ConditionalWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;
use Combyna\Component\Ui\Widget\RepeaterWidgetInterface;
use Combyna\Component\Ui\Widget\TextWidgetInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetGroupInterface;
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
    public function createChildReferenceWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        ChildReferenceWidgetInterface $widget,
        ChildReferenceWidgetStateInterface $widgetState = null
    ) {
        return new ChildReferenceWidgetEvaluationContext(
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
    public function createCompoundWidgetDefinitionEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        WidgetDefinitionInterface $widgetDefinition,
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
    public function createConditionalWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        ConditionalWidgetInterface $widget,
        ConditionalWidgetStateInterface $widgetState = null
    ) {
        return new ConditionalWidgetEvaluationContext(
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
    public function createNullRootContext()
    {
        return $this->parentContextFactory->createNullRootContext();
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
    public function createRepeaterWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        RepeaterWidgetInterface $widget,
        RepeaterWidgetStateInterface $widgetState = null
    ) {
        return new RepeaterWidgetEvaluationContext(
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
    public function createRootContext(ResourceRepositoryInterface $resourceRepository)
    {
        return $this->parentContextFactory->createRootContext($resourceRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootViewEvaluationContext(
        ViewInterface $view,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment,
        RouterStateInterface $routerState,
        PageViewStateInterface $pageViewState = null
    ) {
        return new RootViewEvaluationContext(
            $this,
            $view,
            $parentContext,
            $environment,
            $routerState,
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
    public function createTextWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        TextWidgetInterface $widget,
        TextWidgetStateInterface $widgetState = null
    ) {
        return new TextWidgetEvaluationContext(
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
            $viewState->getRouterState(),
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
     * Creates a RepeaterWidgetEvaluationContext from a RepeaterWidgetState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param RepeaterWidgetStateInterface $widgetState
     * @param ProgramInterface $program
     * @return RepeaterWidgetEvaluationContextInterface
     */
    public function createRepeaterEvaluationContextFromRepeaterStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        RepeaterWidgetStateInterface $widgetState,
        ProgramInterface $program
    ) {
        /** @var RepeaterWidgetInterface $widget */
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        return $this->createRepeaterWidgetEvaluationContext(
            $parentContext,
            $widget,
            $widgetState
        );
    }

    /**
     * Creates a ScopeEvaluationContext for a repeated child of a repeater
     *
     * @param RepeaterWidgetEvaluationContextInterface $repeaterContext
     * @param RepeaterWidgetStateInterface $repeaterWidgetState
     * @param WidgetStatePathInterface $childWidgetStatePath
     * @param WidgetStateInterface $childWidgetState
     * @param ProgramInterface $program
     * @return ViewEvaluationContextInterface
     */
    public function createRepeaterRepeatedChildEvaluationContextFromChildStatePath(
        RepeaterWidgetEvaluationContextInterface $repeaterContext,
        RepeaterWidgetStateInterface $repeaterWidgetState,
        WidgetStatePathInterface $childWidgetStatePath,
        WidgetStateInterface $childWidgetState,
        ProgramInterface $program
    ) {
        $variableStatics = [
            $repeaterWidgetState->getItemVariableName() => $repeaterWidgetState
                ->getItemStatic($childWidgetState->getStateName())
        ];

        if ($repeaterWidgetState->getIndexVariableName() !== null) {
            $variableStatics[$repeaterWidgetState->getIndexVariableName()] =
                $this->staticExpressionFactory->createNumberExpression($childWidgetState->getStateName());
        }

        return $this->createViewEvaluationContext(
            $repeaterContext,
            $this->bagFactory->createStaticBag($variableStatics)
        );
    }

    /**
     * Creates a DefinedWidgetEvaluationContext from a DefinedWidgetState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param DefinedCompoundWidgetStateInterface $widgetState
     * @param ProgramInterface $program
     * @return DefinedWidgetEvaluationContextInterface
     */
    public function createWidgetEvaluationContextFromDefinedCompoundWidgetStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        DefinedCompoundWidgetStateInterface $widgetState,
        ProgramInterface $program
    ) {
        /** @var DefinedWidgetInterface $widget */
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        $definitionEvaluationContext = $this->createCompoundWidgetDefinitionEvaluationContext(
            $parentContext,
            $widget->getDefinition(),
            $widget,
            $widgetState
        );

        $evaluationContext = $widget->createEvaluationContext($definitionEvaluationContext, $this, $widgetState);

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
     * Creates a DefinedWidgetEvaluationContext from a DefinedWidgetState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param DefinedPrimitiveWidgetStateInterface $widgetState
     * @param ProgramInterface $program
     * @return DefinedWidgetEvaluationContextInterface
     */
    public function createWidgetEvaluationContextFromDefinedPrimitiveWidgetStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        DefinedPrimitiveWidgetStateInterface $widgetState,
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
     * Creates a ChildReferenceWidgetEvaluationContext from a ChildReferenceWidgetState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param ChildReferenceWidgetStateInterface $widgetState
     * @param ProgramInterface $program
     * @return ChildReferenceWidgetEvaluationContextInterface
     */
    public function createChildReferenceWidgetEvaluationContextFromChildReferenceWidgetStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        ChildReferenceWidgetStateInterface $widgetState,
        ProgramInterface $program
    ) {
        /** @var ChildReferenceWidgetInterface $widget */
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        return $this->createChildReferenceWidgetEvaluationContext(
            $parentContext,
            $widget,
            $widgetState
        );
    }

    /**
     * Creates a WidgetGroupEvaluationContext from a WidgetGroupState
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetStatePathInterface $widgetStatePath
     * @param WidgetGroupStateInterface $widgetState
     * @param ProgramInterface $program
     * @return WidgetGroupEvaluationContextInterface
     */
    public function createWidgetGroupEvaluationContextFromWidgetGroupStatePath(
        ViewEvaluationContextInterface $parentContext,
        WidgetStatePathInterface $widgetStatePath,
        WidgetGroupStateInterface $widgetState,
        ProgramInterface $program
    ) {
        /** @var WidgetGroupInterface $widget */
        $widget = $program->getWidgetByPath($widgetStatePath->getWidgetPath());

        return $this->createWidgetGroupEvaluationContext(
            $parentContext,
            $widget,
            $widgetState
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createWidgetGroupEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        WidgetGroupInterface $widget,
        WidgetGroupStateInterface $widgetState = null
    ) {
        return new WidgetGroupEvaluationContext(
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
    public function getParentStateTypeToContextFactoryMap()
    {
        return [
            RepeaterWidgetStateInterface::TYPE => [$this, 'createRepeaterRepeatedChildEvaluationContextFromChildStatePath']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getStateTypeToContextFactoryMap()
    {
        return [
            ChildReferenceWidgetStateInterface::TYPE => [$this, 'createChildReferenceWidgetEvaluationContextFromChildReferenceWidgetStatePath'],
            DefinedCompoundWidgetStateInterface::TYPE => [$this, 'createWidgetEvaluationContextFromDefinedCompoundWidgetStatePath'],
            DefinedPrimitiveWidgetStateInterface::TYPE => [$this, 'createWidgetEvaluationContextFromDefinedPrimitiveWidgetStatePath'],
            PageViewStateInterface::TYPE => [$this, 'createPageViewEvaluationContextFromPageViewStatePath'],
            RepeaterWidgetStateInterface::TYPE => [$this, 'createRepeaterEvaluationContextFromRepeaterStatePath'],
            WidgetGroupStateInterface::TYPE => [$this, 'createWidgetGroupEvaluationContextFromWidgetGroupStatePath']
        ];
    }
}
