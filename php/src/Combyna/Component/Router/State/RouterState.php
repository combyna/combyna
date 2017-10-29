<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\State;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Router\RouteInterface;

/**
 * Class RouterState
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouterState implements RouterStateInterface
{
    /**
     * @var RouteInterface
     */
    private $route;

    /**
     * @var StaticBagInterface
     */
    private $routeArgumentBag;

    /**
     * @param RouteInterface $route
     * @param StaticBagInterface $routeArgumentBag
     */
    public function __construct(RouteInterface $route, StaticBagInterface $routeArgumentBag)
    {
        $this->route = $route;
        $this->routeArgumentBag = $routeArgumentBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteArgumentBag()
    {
        return $this->routeArgumentBag;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutePageViewName()
    {
        return $this->route->getPageViewName();
    }

    /**
     * {@inheritdoc}
     */
    public function withRoute(RouteInterface $route, StaticBagInterface $routeArgumentBag)
    {
        if ($route === $this->route && $routeArgumentBag === $this->routeArgumentBag) {
            // Route has not changed: no need to create a new state
            return $this;
        }

        return new self($route, $routeArgumentBag);
    }
}
