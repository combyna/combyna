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

use Combyna\Component\Ui\State\View\ViewStateInterface;

/**
 * Interface OverlayViewCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface OverlayViewCollectionInterface extends ViewCollectionInterface
{
    /**
     * Creates an initial state for all visible overlay views
     *
     * @return ViewStateInterface[]
     */
    public function createInitialStates();

    /**
     * {@inheritdoc}
     *
     * @return OverlayViewInterface
     */
    public function getView($viewName);

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function hasView($name);
}
