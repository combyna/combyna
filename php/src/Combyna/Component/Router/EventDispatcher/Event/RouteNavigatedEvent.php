<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\EventDispatcher\Event;

use Combyna\Component\Router\State\RouterStateInterface;

/**
 * Class RouteNavigatedEvent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteNavigatedEvent extends AbstractRouterEvent
{
    /**
     * @var RouterStateInterface
     */
    private $routerState;

    /**
     * @param RouterStateInterface $routerState
     */
    public function __construct(RouterStateInterface $routerState)
    {
        $this->routerState = $routerState;
    }

    /**
     * Fetches the new router state that resulted from the navigation
     *
     * @return RouterStateInterface
     */
    public function getRouterState()
    {
        return $this->routerState;
    }

    /**
     * Fetches the new URL after the navigation
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->routerState->getRoute()->generateUrl($this->routerState->getRouteArgumentBag());
    }
}
