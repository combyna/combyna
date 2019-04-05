<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router\Config\Loader;

use Combyna\Component\Router\Config\Act\RouteNodeInterface;

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
     * @param string $libraryName
     * @param string $routeName
     * @param array $routeConfig
     * @return RouteNodeInterface
     */
    public function loadRoute($libraryName, $routeName, array $routeConfig);

    /**
     * Creates a collection of RouteNodes from the given environment config
     *
     * @param string $libraryName
     * @param array $collectionConfig
     * @return RouteNodeInterface[]
     */
    public function loadRouteCollection($libraryName, array $collectionConfig);
}
