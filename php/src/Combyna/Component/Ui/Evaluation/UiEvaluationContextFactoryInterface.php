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
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\View\PageViewStateInterface;
use Combyna\Component\Ui\Store\Evaluation\ViewStoreEvaluationContextInterface;
use Combyna\Component\Ui\View\ViewInterface;
use Combyna\Component\Ui\Widget\CoreWidgetInterface;
use Combyna\Component\Ui\Widget\DefinedWidgetInterface;

/**
 * Interface UiEvaluationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiEvaluationContextFactoryInterface extends EvaluationContextFactoryInterface
{
    /**
     * Creates a CoreWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param CoreWidgetInterface $widget
     * @return CoreWidgetEvaluationContextInterface
     */
    public function createCoreWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        CoreWidgetInterface $widget
    );

    /**
     * Creates a DefinedWidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param DefinedWidgetInterface $widget
     * @return DefinedWidgetEvaluationContextInterface
     */
    public function createDefinedWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        DefinedWidgetInterface $widget
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
     * @param EvaluationContextInterface $parentContext
     * @param UiStoreStateInterface $viewStoreState
     * @return ViewStoreEvaluationContextInterface
     */
    public function createViewStoreEvaluationContext(
        EvaluationContextInterface $parentContext,
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
