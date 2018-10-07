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
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactoryInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Ui\State\Store\UiStoreStateInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\State\Widget\CoreWidgetStateInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContextInterface;
use Combyna\Component\Ui\View\ViewInterface;
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
     * Creates a CompoundWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param ExpressionBagInterface $valueExpressionBag
     * @return CompoundWidgetEvaluationContextInterface
     */
    public function createCompoundWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        ExpressionBagInterface $valueExpressionBag
    );

    /**
     * Creates a CoreWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param CoreWidgetInterface $widget
     * @param CoreWidgetStateInterface $widgetState
     * @return CoreWidgetEvaluationContextInterface
     */
    public function createCoreWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        CoreWidgetInterface $widget,
        CoreWidgetStateInterface $widgetState
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
     * Creates a PrimitiveWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param PrimitiveWidgetDefinition $widgetDefinition
     * @param DefinedWidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @return PrimitiveWidgetEvaluationContextInterface
     */
    public function createPrimitiveWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        PrimitiveWidgetDefinition $widgetDefinition,
        DefinedWidgetInterface $widget,
        StaticBagInterface $attributeStaticBag
    );

    /**
     * Creates a RootViewEvaluationContext
     *
     * @param ViewInterface $view
     * @param ViewStoreStateInterface $viewStoreState
     * @param StaticBagInterface $viewAttributeStaticBag
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     * @return RootViewEvaluationContext
     */
    public function createRootViewEvaluationContext(
        ViewInterface $view,
        ViewStoreStateInterface $viewStoreState,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment
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
