<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\View;

use Combyna\Component\Ui\Config\Act\PageViewNode;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;

/**
 * Interface PageViewStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface PageViewStateInterface extends ViewStateInterface
{
    const TYPE = PageViewNode::TYPE;

    /**
     * Either creates a new page view state with the specified root widget state
     * or just returns the current one, if it already has the same state
     *
     * @param WidgetStateInterface $newRootWidgetState
     * @return PageViewStateInterface
     */
    public function withRootWidgetState(WidgetStateInterface $newRootWidgetState);

    /**
     * Either creates a new page view state with the specified store and root widget states
     * or just returns the current one, if it already has the same states
     *
     * @param ViewStoreStateInterface $newStoreState
     * @param WidgetStateInterface $newRootWidgetState
     * @return PageViewStateInterface
     */
    public function withState(ViewStoreStateInterface $newStoreState, WidgetStateInterface $newRootWidgetState);

    /**
     * Either creates a new page view state with the specified store state
     * or just returns the current one, if it already has the same state
     *
     * @param ViewStoreStateInterface $newStoreState
     * @return PageViewStateInterface
     */
    public function withStoreState(ViewStoreStateInterface $newStoreState);
}
