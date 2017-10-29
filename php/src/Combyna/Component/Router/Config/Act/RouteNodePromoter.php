<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Router\RouteCollectionInterface;
use Combyna\Component\Router\RouterFactoryInterface;

/**
 * Class RouteNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var RouterFactoryInterface
     */
    private $routerFactory;

    /**
     * @param RouterFactoryInterface $routerFactory
     * @param BagNodePromoter $bagNodePromoter
     */
    public function __construct(RouterFactoryInterface $routerFactory, BagNodePromoter $bagNodePromoter)
    {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->routerFactory = $routerFactory;
    }

    /**
     * Promotes the provided list of RouteNodes to actual Route instances
     *
     * @param RouteNode[] $routeNodes
     * @return RouteCollectionInterface
     */
    public function promoteCollection(array $routeNodes)
    {
        $routes = [];

        foreach ($routeNodes as $routeNode) {
            $routes[$routeNode->getName()] = $this->routerFactory->createRoute(
                $routeNode->getName(),
                $this->bagNodePromoter->promoteFixedStaticBagModel($routeNode->getAttributeBagModel()),
                $routeNode->getPageViewName()
            );
        }

        return $this->routerFactory->createRouteCollection($routes);
    }
}
