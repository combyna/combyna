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
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Ui\State\View\ViewStateInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;
use InvalidArgumentException;

/**
 * Interface ViewCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewCollectionInterface
{
    /**
     * Fetches a page from this collection by its name
     *
     * @param string $viewName
     * @return ViewInterface
     */
    public function getView($viewName);

    /**
     * Fetches a widget using its name-based path
     *
     * @param string[] $names
     * @return WidgetInterface
     */
    public function getWidgetByPath(array $names);

    /**
     * Returns true if a view exists in this collection with the specified name, false otherwise
     *
     * @param string $viewName
     * @return bool
     */
    public function hasView($viewName);

    /**
     * Renders the specified view, or returns null if invisible
     *
     * @param string $viewName
     * @param StaticBagInterface $viewAttributeStaticBag
     * @param EvaluationContextInterface $rootEvaluationContext
     * @return ViewStateInterface|null
     * @throws InvalidArgumentException Throws when the specified view does not exist
     */
    public function renderView(
        $viewName,
        StaticBagInterface $viewAttributeStaticBag,
        EvaluationContextInterface $rootEvaluationContext
    );
}
