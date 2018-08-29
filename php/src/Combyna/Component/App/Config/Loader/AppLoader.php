<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App\Config\Loader;

use Combyna\Component\App\AppFactoryInterface;
use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Router\Config\Loader\RouteCollectionLoaderInterface;
use Combyna\Component\Signal\Config\Loader\SignalDefinitionLoaderInterface;
use Combyna\Component\Ui\Config\Loader\ViewCollectionLoaderInterface;
use Symfony\Component\Translation\Translator;

/**
 * Class AppLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AppLoader implements AppLoaderInterface
{
    /**
     * @var AppFactoryInterface
     */
    private $appFactory;

    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var HomeLoaderInterface
     */
    private $homeLoader;

    /**
     * @var RouteCollectionLoaderInterface
     */
    private $routeCollectionLoader;

    /**
     * @var SignalDefinitionLoaderInterface
     */
    private $signalDefinitionLoader;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var ViewCollectionLoaderInterface
     */
    private $viewCollectionLoader;

    /**
     * @param ConfigParser $configParser
     * @param AppFactoryInterface $appFactory
     * @param HomeLoaderInterface $homeLoader
     * @param ViewCollectionLoaderInterface $viewCollectionLoader
     * @param Translator $translator
     * @param RouteCollectionLoaderInterface $routeCollectionLoader
     * @param SignalDefinitionLoaderInterface $signalDefinitionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        AppFactoryInterface $appFactory,
        HomeLoaderInterface $homeLoader,
        ViewCollectionLoaderInterface $viewCollectionLoader,
        Translator $translator,
        RouteCollectionLoaderInterface $routeCollectionLoader,
        SignalDefinitionLoaderInterface $signalDefinitionLoader
    ) {
        $this->appFactory = $appFactory;
        $this->configParser = $configParser;
        $this->homeLoader = $homeLoader;
        $this->routeCollectionLoader = $routeCollectionLoader;
        $this->signalDefinitionLoader = $signalDefinitionLoader;
        $this->translator = $translator;
        $this->viewCollectionLoader = $viewCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadApp(EnvironmentNode $environmentNode, array $appConfig)
    {
        // Load any translations from the app config into Symfony's translator
        // so that they will be available later
        if (array_key_exists('translations', $appConfig) && is_array($appConfig['translations'])) {
            foreach ($appConfig['translations'] as $locale => $messages) {
                if (!is_array($messages)) {
                    continue;
                }

                $namespacedMessages = [];

                foreach ($messages as $key => $message) {
                    $namespacedMessages['app.' . $key] = $message;
                }

                $this->translator->addResource('array', $namespacedMessages, $locale);
            }
        }

        $signalDefinitionConfigs = $this->configParser->getOptionalElement(
            $appConfig,
            'signals',
            'signal definitions',
            [],
            'array'
        );

        $signalDefinitionNodes = [];

        foreach ($signalDefinitionConfigs as $signalName => $signalDefinitionConfig) {
            $signalDefinitionNodes[] = $this->signalDefinitionLoader->load(
                AppNode::TYPE,
                $signalName,
                $signalDefinitionConfig
            );
        }

        $routeNodes = $this->routeCollectionLoader->loadRouteCollection(
            $appConfig['routes']
        );

        $homeNode = $this->homeLoader->loadHome($appConfig['home']);

        $pageViewNodes = $this->viewCollectionLoader->loadPageViews(
            $appConfig['page_views'],
            $environmentNode
        );

        $overlayViewNodes = []; // TODO

        return new AppNode(
            $environmentNode,
            $signalDefinitionNodes,
            $routeNodes,
            $homeNode,
            $pageViewNodes,
            $overlayViewNodes
        );
    }
}
