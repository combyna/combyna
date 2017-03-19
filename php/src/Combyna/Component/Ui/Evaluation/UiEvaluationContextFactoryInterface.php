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
use Combyna\Component\Ui\ViewInterface;
use Combyna\Component\Ui\WidgetInterface;

/**
 * Interface UiEvaluationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiEvaluationContextFactoryInterface extends EvaluationContextFactoryInterface
{
    /**
     * Creates a RootViewEvaluationContext
     *
     * @param ViewInterface $view
     * @param StaticBagInterface $viewAttributeStaticBag
     * @param EvaluationContextInterface $parentContext
     * @param EnvironmentInterface $environment
     * @return RootViewEvaluationContext
     */
    public function createRootViewEvaluationContext(
        ViewInterface $view,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $parentContext,
        EnvironmentInterface $environment
    );

    /**
     * Creates a ViewEvaluationContext
     *
     * @param EvaluationContextInterface $parentContext
     * @param StaticBagInterface $variableStaticBag
     * @return ViewEvaluationContextInterface
     */
    public function createViewEvaluationContext(
        EvaluationContextInterface $parentContext,
        StaticBagInterface $variableStaticBag
    );

    /**
     * Creates a WidgetEvaluationContext
     *
     * @param ViewEvaluationContextInterface $parentContext
     * @param WidgetInterface $widget
     * @return WidgetEvaluationContextInterface
     */
    public function createWidgetEvaluationContext(
        ViewEvaluationContextInterface $parentContext,
        WidgetInterface $widget
    );
}
