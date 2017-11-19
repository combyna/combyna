<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\View;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Ui\Evaluation\UiEvaluationContextInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface ViewInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewInterface
{
    /**
     * Fetches the description of this view
     *
     * @return string
     */
    public function getDescription();

    /**
     * Fetches the unique name for this view
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches a widget in this view by its name-based path
     *
     * @param string[] $names
     * @return WidgetInterface
     */
    public function getWidgetByPath(array $names);

    /**
     * Makes a query for the store of this view
     *
     * @param string $queryName
     * @param StaticBagInterface $argumentStaticBag
     * @param UiEvaluationContextInterface $evaluationContext
     * @param ViewStoreStateInterface $viewStoreState
     * @return StaticInterface
     */
    public function makeStoreQuery(
        $queryName,
        StaticBagInterface $argumentStaticBag,
        UiEvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    );

//    /**
//     * Renders this view to a ViewState
//     *
//     * @param StaticBagInterface $viewAttributeStaticBag
//     * @param EvaluationContextInterface $rootEvaluationContext
//     * @return ViewStateInterface|null Returns the rendered view or null if invisible
//     */
//    public function render(
//        StaticBagInterface $viewAttributeStaticBag,
//        EvaluationContextInterface $rootEvaluationContext
//    );
}
