<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Act;

use Combyna\Component\App\AppFactoryInterface;
use Combyna\Component\App\HomeInterface;
use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Router\RouteRepositoryInterface;

/**
 * Class HomeNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HomeNodePromoter
{
    /**
     * @var AppFactoryInterface
     */
    private $appFactory;

    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @param AppFactoryInterface $appFactory
     * @param BagNodePromoter $bagNodePromoter
     */
    public function __construct(
        AppFactoryInterface $appFactory,
        BagNodePromoter $bagNodePromoter
    ) {
        $this->appFactory = $appFactory;
        $this->bagNodePromoter = $bagNodePromoter;
    }

    /**
     * Promotes an HomeNode to a Home
     *
     * @param HomeNode $homeNode
     * @param RouteRepositoryInterface $routeRepository
     * @return HomeInterface
     */
    public function promoteHome(HomeNode $homeNode, RouteRepositoryInterface $routeRepository)
    {
        // Look the route up - it could come from the app itself (using the special "app" library name)
        // or an installed library
        $route = $routeRepository->getByName($homeNode->getRouteLibraryName(), $homeNode->getRouteName());

        $attributeExpressionBag = $this->bagNodePromoter->promoteExpressionBag($homeNode->getAttributeExpressionBag());

        return $this->appFactory->createHome($route, $attributeExpressionBag);
    }
}
