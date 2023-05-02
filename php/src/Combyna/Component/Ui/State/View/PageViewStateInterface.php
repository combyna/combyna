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

use Combyna\Component\Router\State\RouterStateInterface;
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
     * Fetches the router state
     *
     * @return RouterStateInterface
     */
    public function getRouterState();

    /**
     * Fetches the current title of the page.
     *
     * @return string
     */
    public function getTitle();

    /**
     * Either creates a new page view state with the specified root widget state
     * or just returns the current one, if it already has the same state.
     *
     * @param WidgetStateInterface $newRootWidgetState
     * @return PageViewStateInterface
     */
    public function withRootWidgetState(WidgetStateInterface $newRootWidgetState);

    /**
     * Either creates a new page view state with the specified store state
     * or just returns the current one, if it already has the same state.
     *
     * @param ViewStoreStateInterface $newStoreState
     * @return PageViewStateInterface
     */
    public function withStoreState(ViewStoreStateInterface $newStoreState);

    /**
     * Either creates a new page view state with the specified title
     * or just returns the current one, if it already has the same title.
     *
     * @param string $title
     * @return PageViewStateInterface
     */
    public function withTitle($title);
}
