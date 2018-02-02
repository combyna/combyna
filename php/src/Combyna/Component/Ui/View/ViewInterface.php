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
use Combyna\Component\Ui\Evaluation\ViewEvaluationContextInterface;
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
     * @param ViewEvaluationContextInterface $evaluationContext
     * @param ViewStoreStateInterface $viewStoreState
     * @return StaticInterface
     */
    public function makeStoreQuery(
        $queryName,
        StaticBagInterface $argumentStaticBag,
        ViewEvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    );
}
