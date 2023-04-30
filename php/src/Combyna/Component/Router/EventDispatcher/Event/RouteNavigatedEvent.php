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

use Combyna\Component\Bag\StaticBagInterface;
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
     * Fetches the name of the library of the route that was navigated to.
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->routerState->getRoute()->getLibraryName();
    }

    /**
     * Fetches the argument static bag for the current route.
     *
     * @return StaticBagInterface
     */
    public function getRouteArgumentBag()
    {
        return $this->routerState->getRouteArgumentBag();
    }

    /**
     * Fetches the name of the route (within its library) that was navigated to.
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routerState->getRoute()->getRouteName();
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
