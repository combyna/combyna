<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\State;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Router\RouteInterface;

/**
 * Interface RouterStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouterStateInterface
{
    /**
     * Fetches the current route for this state
     *
     * @return RouteInterface
     */
    public function getRoute();

    /**
     * Fetches the argument static bag for the current route
     *
     * @return StaticBagInterface
     */
    public function getRouteArgumentBag();

    /**
     * Fetches the name of the page view that the current route should render
     *
     * @return string
     */
    public function getRoutePageViewName();

    /**
     * Creates a new RouterState identical to this one, but with the specified route and argument bag
     *
     * @param RouteInterface $route
     * @param StaticBagInterface $routeArgumentBag
     * @return RouterStateInterface
     */
    public function withRoute(RouteInterface $route, StaticBagInterface $routeArgumentBag);
}
