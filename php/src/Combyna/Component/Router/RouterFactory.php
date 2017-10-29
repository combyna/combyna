<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Router;

use Combyna\Component\App\HomeInterface;
use Combyna\Component\Bag\BagFactoryInterface;
use Combyna\Component\Bag\FixedStaticBagModelInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Signal\DispatcherInterface;
use Combyna\Component\Signal\SignalDefinitionRepositoryInterface;

/**
 * Class RouterFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouterFactory implements RouterFactoryInterface
{
    /**
     * @var BagFactoryInterface
     */
    private $bagFactory;

    /**
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * @param BagFactoryInterface $bagFactory
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(
        BagFactoryInterface $bagFactory,
        DispatcherInterface $dispatcher
    ) {
        $this->bagFactory = $bagFactory;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function createRoute($name, FixedStaticBagModelInterface $attributeStaticBagModel, $pageViewName)
    {
        return new Route($name, $attributeStaticBagModel, $pageViewName);
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
            $this->dispatcher,
            $routeRepository,
            $home,
            $signalDefinitionRepository,
            $this->bagFactory
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
