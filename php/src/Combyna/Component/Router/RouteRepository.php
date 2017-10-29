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

use Combyna\Component\App\AppInterface;
use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Environment\Library\LibraryInterface;

/**
 * Class RouteRepository
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteRepository implements RouteRepositoryInterface
{
    /**
     * @var RouteCollectionInterface
     */
    private $appRouteCollection;

    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @param EnvironmentInterface $environment
     * @param RouteCollectionInterface $appRouteCollection
     */
    public function __construct(EnvironmentInterface $environment, RouteCollectionInterface $appRouteCollection)
    {
        $this->appRouteCollection = $appRouteCollection;
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($libraryName, $routeName)
    {
        if ($libraryName === LibraryInterface::APP) {
            return $this->appRouteCollection->getByName($routeName);
        }

        return $this->environment->getRouteByName($libraryName, $routeName);
    }
}
