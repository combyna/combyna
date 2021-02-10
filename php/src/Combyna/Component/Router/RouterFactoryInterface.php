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

use Combyna\Component\App\HomeInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;

/**
 * Interface RouterFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RouterFactoryInterface
{
    /**
     * Creates a new Route
     *
     * @param string $name
     * @param FixedStaticBagModelInterface $attributeStaticBagModel
     * @param string $pageViewName
     * @return RouteInterface
     */
    public function createRoute($name, FixedStaticBagModelInterface $attributeStaticBagModel, $pageViewName);

    /**
     * Creates a new RouteCollection
     *
     * @param RouteInterface[] $routes
     * @return RouteCollectionInterface
     */
    public function createRouteCollection(array $routes);

    /**
     * Creates a new Router
     *
     * @param RouteRepositoryInterface $routeRepository
     * @param HomeInterface $home
     * @param SignalDefinitionRepositoryInterface $signalDefinitionRepository
     * @return RouterInterface
     */
    public function createRouter(
        RouteRepositoryInterface $routeRepository,
        HomeInterface $home,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository
    );

    /**
     * Creates a new RouteRepository
     *
     * @param EnvironmentInterface $environment
     * @param RouteCollectionInterface $appRouteCollection
     * @return RouteRepositoryInterface
     */
    public function createRouteRepository(
        EnvironmentInterface $environment,
        RouteCollectionInterface $appRouteCollection
    );
}
