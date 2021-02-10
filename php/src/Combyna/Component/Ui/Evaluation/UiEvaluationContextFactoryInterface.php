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
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\CoreWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedCompoundWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\DefinedPrimitiveWidgetStateInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContextInterface;
use Combyna\Component\Ui\View\ViewInterface;
use Combyna\Component\Ui\Widget\CompoundWidgetDefinition;
use Combyna\Component\Ui\Widget\CoreWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;
use Combyna\Component\Ui\Widget\PrimitiveWidgetDefinition;

/**
 * Interface UiEvaluationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiEvaluationContextFactoryInterface extends EvaluationContextFactoryInterface
{
    /**
     * Creates a CompoundWidgetDefinitionEvaluationContext
     *
     * @param CompoundWidgetEvaluationContextInterface $parentContext
     * @param CompoundWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param DefinedCompoundWidgetStateInterface|null $widgetState
     * @return CompoundWidgetDefinitionEvaluationContextInterface
     */
    public function createCompoundWidgetDefinitionEvaluationContext(
        CompoundWidgetEvaluationContextInterface $parentContext,
        CompoundWidgetDefinition $widgetDefinition,
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
     * Creates a CoreWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param CoreWidgetInterface $widget
     * @param CoreWidgetStateInterface|null $widgetState
     * @return CoreWidgetEvaluationContextInterface
     */
    public function createCoreWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        CoreWidgetInterface $widget,
        CoreWidgetStateInterface $widgetState = null
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
     * Creates a RootViewEvaluationContext
     *
     * @param ViewInterface $view
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     * @param PageViewStateInterface|null $pageViewState
     * @return RootViewEvaluationContext
     */
    public function createRootViewEvaluationContext(
        ViewInterface $view,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment,
        PageViewStateInterface $pageViewState = null
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
}
