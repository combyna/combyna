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

use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Loader\ConfigParserInterface;
use Combyna\Component\Config\Parameter\CallbackOptionalParameter;
use Combyna\Component\Config\Parameter\NamedParameter;
use Combyna\Component\Config\Parameter\Type\FixedStaticBagModelParameterType;
use Combyna\Component\Config\Parameter\Type\StringParameterType;
use Combyna\Component\Environment\Config\Loader\Library\LibraryLoaderInterface;
use Combyna\Component\Router\Config\Act\InvalidRouteNode;
use Combyna\Component\Router\Config\Act\RouteNode;

/**
 * Class RouteCollectionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RouteCollectionLoader implements RouteCollectionLoaderInterface
{
    /**
     * @var ConfigParserInterface
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
     * @param ConfigParserInterface $configParser
     * @param LibraryLoaderInterface $libraryLoader
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     */
    public function __construct(
        ConfigParserInterface $configParser,
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
    public function loadRoute($libraryName, $routeName, array $routeConfig)
    {
        try {
            $parsedArgumentBag = $this->configParser->parseArguments($routeConfig, [
                new NamedParameter('pattern', new StringParameterType('URL pattern')),
                new CallbackOptionalParameter(
                    new NamedParameter(
                        'parameters',
                        new FixedStaticBagModelParameterType('URL parameter model')
                    ),
                    function () {
                        return new FixedStaticBagModelNode([]);
                    }
                ),
                new NamedParameter('page_view', new StringParameterType('page view name'))
            ]);
        } catch (ArgumentParseException $exception) {
            return new InvalidRouteNode($libraryName, $routeName, $exception->getMessage());
        }

        $urlPattern = $parsedArgumentBag->getNamedStringArgument('pattern');
        $parameterBagModelNode = $parsedArgumentBag->getNamedFixedStaticBagModelArgument('parameters');
        $pageViewName = $parsedArgumentBag->getNamedStringArgument('page_view');

        return new RouteNode($routeName, $urlPattern, $parameterBagModelNode, $pageViewName);
    }

    /**
     * {@inheritdoc}
     */
    public function loadRouteCollection($libraryName, array $collectionConfig)
    {
        $routeNodes = [];

        foreach ($collectionConfig as $routeName => $routeConfig) {
            $routeNodes[] = $this->loadRoute($libraryName, $routeName, $routeConfig);
        }

        return $routeNodes;
    }
}
