<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Loader;

use Combyna\Component\Router\Config\Act\RouteNode;

/**
 * Interface RouteCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouteCollectionLoaderInterface
{
    /**
     * Creates a RouteNode from the given config
     *
     * @param string $routeName
     * @param array $routeConfig
     * @return RouteNode
     */
    public function loadRoute($routeName, array $routeConfig);

    /**
     * Creates a collection of RouteNodes from the given environment config
     *
     * @param array $collectionConfig
     * @return RouteNode[]
     */
    public function loadRouteCollection(array $collectionConfig);
}
