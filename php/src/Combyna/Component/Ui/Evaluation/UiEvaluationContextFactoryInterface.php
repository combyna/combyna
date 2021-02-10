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
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\ChildReferenceWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\ConditionalWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\TextWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContextInterface;
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

/**
 * Interface UiEvaluationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiEvaluationContextFactoryInterface extends EvaluationContextFactoryInterface
{
    /**
     * Creates a ChildReferenceWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param ChildReferenceWidgetInterface $widget
     * @param ChildReferenceWidgetStateInterface|null $widgetState
     * @return ChildReferenceWidgetEvaluationContextInterface
     */
    public function createChildReferenceWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        ChildReferenceWidgetInterface $widget,
        ChildReferenceWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a CompoundWidgetDefinitionEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetDefinitionInterface $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedCompoundWidgetStateInterface|null $widgetState
     * @return CompoundWidgetDefinitionEvaluationContextInterface
     */
    public function createCompoundWidgetDefinitionEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        WidgetDefinitionInterface $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedCompoundWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a CompoundWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param CompoundWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedCompoundWidgetStateInterface|null $widgetState
     * @return CompoundWidgetEvaluationContextInterface
     */
    public function createCompoundWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        CompoundWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedCompoundWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a ConditionalWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param ConditionalWidgetInterface $widget
     * @param ConditionalWidgetStateInterface|null $widgetState
     * @return ConditionalWidgetEvaluationContextInterface
     */
    public function createConditionalWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        ConditionalWidgetInterface $widget,
        ConditionalWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a ViewEvaluationContext from a PageViewState
     *
     * @param EvaluationContextInterface $parentContext
     * @param PageViewStateInterface $viewState
     * @param ProgramInterface $program
     * @param EnvironmentInterface $environment
     * @return ViewEvaluationContext
     */
    public function createPageViewEvaluationContextFromPageViewState(
        EvaluationContextInterface $parentContext,
        PageViewStateInterface $viewState,
        ProgramInterface $program,
        EnvironmentInterface $environment
    );

    /**
     * Creates a PrimitiveWidgetDefinitionEvaluationContext
     *
     * @param PrimitiveWidgetEvaluationContextInterface $parentContext
     * @param PrimitiveWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedPrimitiveWidgetStateInterface|null $widgetState
     * @return PrimitiveWidgetDefinitionEvaluationContextInterface
     */
    public function createPrimitiveWidgetDefinitionEvaluationContext(
        PrimitiveWidgetEvaluationContextInterface $parentContext,
        PrimitiveWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedPrimitiveWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a PrimitiveWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param PrimitiveWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedPrimitiveWidgetStateInterface|null $widgetState
     * @return PrimitiveWidgetEvaluationContextInterface
     */
    public function createPrimitiveWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        PrimitiveWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        DefinedPrimitiveWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a RepeaterWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param RepeaterWidgetInterface $widget
     * @param RepeaterWidgetStateInterface|null $widgetState
     * @return RepeaterWidgetEvaluationContextInterface
     */
    public function createRepeaterWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        RepeaterWidgetInterface $widget,
        RepeaterWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a RootViewEvaluationContext
     *
     * @param ViewInterface $view
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     * @param RouterStateInterface $routerState
     * @param PageViewStateInterface|null $pageViewState
     * @return RootViewEvaluationContext
     */
    public function createRootViewEvaluationContext(
        ViewInterface $view,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment,
        RouterStateInterface $routerState,
        PageViewStateInterface $pageViewState = null
    );

    /**
     * Creates a TextWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param TextWidgetInterface $widget
     * @param TextWidgetStateInterface|null $widgetState
     * @return TextWidgetEvaluationContextInterface
     */
    public function createTextWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        TextWidgetInterface $widget,
        TextWidgetStateInterface $widgetState = null
    );

    /**
     * Creates a ViewStoreEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param UiStoreStateInterface $viewStoreState
     * @return ViewStoreEvaluationContextInterface
     */
    public function createViewStoreEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        UiStoreStateInterface $viewStoreState
    );

    /**
     * Creates a ViewEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param StaticBagInterface|null $variableStaticBag
     * @return ViewEvaluationContextInterface
     */
    public function createViewEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag = null
    );

    /**
     * Creates a WidgetGroupEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetGroupInterface $widget
     * @param WidgetGroupStateInterface|null $widgetState
     * @return WidgetGroupEvaluationContextInterface
     */
    public function createWidgetGroupEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        WidgetGroupInterface $widget,
        WidgetGroupStateInterface $widgetState = null
    );
}
