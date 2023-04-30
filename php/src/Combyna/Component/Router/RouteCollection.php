<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\Common\Exception\NotFoundException;

/**
 * Class RouteCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteCollection implements RouteCollectionInterface
{
    /**
     * @var RouteInterface[]
     */
    private $routeByName = [];

    /**
     * @param RouteInterface[] $routes
     */
    public function __construct(array $routes)
    {
        foreach ($routes as $route) {
            $this->routeByName[$route->getRouteName()] = $route;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($name)
    {
        if (!array_key_exists($name, $this->routeByName)) {
            throw new NotFoundException('Collection has no route with name "' . $name . '"');
        }

        return $this->routeByName[$name];
    }
}
