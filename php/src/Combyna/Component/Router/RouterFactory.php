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
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class RouterFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouterFactory implements RouterFactoryInterface
{
    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute($name, $urlPattern, FixedStaticBagModelInterface $parameterBagModel, $pageViewName)
    {
        return new Route($name, $urlPattern, $parameterBagModel, $pageViewName);
    }

    /**
     * {@inheritdoc}
     */
    public function createRouteCollection(array $routes)
    {
        return new RouteCollection($routes);
    }

    /**
     * {@inheritdoc}
     */
    public function createRouter(
        RouteRepositoryInterface $routeRepository,
        HomeInterface $home,
        SignalDefinitionRepositoryInterface $signalDefinitionRepository
    ) {
        return new Router(
            $this->eventDispatcher,
            $this->dispatcher,
            $routeRepository,
            $home,
            $signalDefinitionRepository
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createRouteRepository(
        EnvironmentInterface $environment,
        RouteCollectionInterface $appRouteCollection
    ) {
        return new RouteRepository($environment, $appRouteCollection);
    }
}
