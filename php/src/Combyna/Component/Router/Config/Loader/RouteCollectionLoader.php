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

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Config\Loader\Library\LibraryLoaderInterface;
use Combyna\Component\Router\Config\Act\RouteNode;

/**
 * Class EnvironmentLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteCollectionLoader implements RouteCollectionLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var FixedStaticBagModelLoaderInterface
     */
    private $fixedStaticBagModelLoader;

    /**
     * @var LibraryLoaderInterface
     */
    private $libraryLoader;

    /**
     * @param ConfigParser $configParser
     * @param LibraryLoaderInterface $libraryLoader
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     */
    public function __construct(
        ConfigParser $configParser,
        LibraryLoaderInterface $libraryLoader,
        FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
    ) {
        $this->configParser = $configParser;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
        $this->libraryLoader = $libraryLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadRoute($routeName, array $routeConfig)
    {
        $pattern = $this->configParser->getElement(
            $routeConfig,
            'pattern',
            'URL pattern'
        );
        $attributesConfig = $this->configParser->getOptionalElement(
            $routeConfig,
            'attributes',
            'URL attribute model',
            []
        );
        $pageViewName = $this->configParser->getElement(
            $routeConfig,
            'page_view',
            'page view name'
        );

        $attributeBagModelNode = $this->fixedStaticBagModelLoader->load($attributesConfig);

        return new RouteNode($routeName, $attributeBagModelNode, $pageViewName);
    }

    /**
     * {@inheritdoc}
     */
    public function loadRouteCollection(array $collectionConfig)
    {
        $routeNodes = [];

        foreach ($collectionConfig as $routeName => $routeConfig) {
            $routeNodes[] = $this->loadRoute($routeName, $routeConfig);
        }

        return $routeNodes;
    }
}
