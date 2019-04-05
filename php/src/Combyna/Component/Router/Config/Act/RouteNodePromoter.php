<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Router\RouteCollectionInterface;
use Combyna\Component\Router\RouterFactoryInterface;
use Combyna\Component\Validator\Query\Requirement\PromotionQueryRequirement;

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
     * @param RouteNodeInterface[] $routeNodes
     * @return RouteCollectionInterface
     */
    public function promoteCollection(array $routeNodes)
    {
        $routes = [];

        foreach ($routeNodes as $routeNode) {
            $queryRequirement = new PromotionQueryRequirement($routeNode);

            $routes[$routeNode->getName()] = $this->routerFactory->createRoute(
                $routeNode->getName(),
                $routeNode->getUrlPattern(),
                $this->bagNodePromoter->promoteFixedStaticBagModel(
                    $routeNode->getParameterBagModel($queryRequirement)
                ),
                $routeNode->getPageViewName()
            );
        }

        return $this->routerFactory->createRouteCollection($routes);
    }
}
