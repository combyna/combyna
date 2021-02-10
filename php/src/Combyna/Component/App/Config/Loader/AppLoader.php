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
use Combyna\Component\Environment\Library\LibraryInterface;
use Combyna\Component\Router\Config\Loader\RouteCollectionLoaderInterface;
use Combyna\Component\Signal\Config\Loader\SignalDefinitionLoaderInterface;
use Combyna\Component\Ui\Config\Loader\ViewCollectionLoaderInterface;
use Combyna\Component\Ui\Config\Loader\WidgetDefinitionLoaderInterface;
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
     * @var WidgetDefinitionLoaderInterface
     */
    private $widgetDefinitionLoader;

    /**
     * @param ConfigParser $configParser
     * @param AppFactoryInterface $appFactory
     * @param HomeLoaderInterface $homeLoader
     * @param ViewCollectionLoaderInterface $viewCollectionLoader
     * @param Translator $translator
     * @param RouteCollectionLoaderInterface $routeCollectionLoader
     * @param SignalDefinitionLoaderInterface $signalDefinitionLoader
     * @param WidgetDefinitionLoaderInterface $widgetDefinitionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        AppFactoryInterface $appFactory,
        HomeLoaderInterface $homeLoader,
        ViewCollectionLoaderInterface $viewCollectionLoader,
        Translator $translator,
        RouteCollectionLoaderInterface $routeCollectionLoader,
        SignalDefinitionLoaderInterface $signalDefinitionLoader,
        WidgetDefinitionLoaderInterface $widgetDefinitionLoader
    ) {
        $this->appFactory = $appFactory;
        $this->configParser = $configParser;
        $this->homeLoader = $homeLoader;
        $this->routeCollectionLoader = $routeCollectionLoader;
        $this->signalDefinitionLoader = $signalDefinitionLoader;
        $this->translator = $translator;
        $this->viewCollectionLoader = $viewCollectionLoader;
        $this->widgetDefinitionLoader = $widgetDefinitionLoader;
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

        $widgetDefinitionConfigs = $this->configParser->getOptionalElement(
            $appConfig,
            'widgets',
            'widget definitions',
            [],
            'array'
        );

        $signalDefinitionNodes = [];

        foreach ($signalDefinitionConfigs as $signalName => $signalDefinitionConfig) {
            $signalDefinitionNodes[] = $this->signalDefinitionLoader->load(
                LibraryInterface::APP,
                $signalName,
                $signalDefinitionConfig
            );
        }

        $widgetDefinitionNodes = [];

        foreach ($widgetDefinitionConfigs as $widgetName => $widgetDefinitionConfig) {
            $widgetDefinitionNodes[] = $this->widgetDefinitionLoader->load(
                LibraryInterface::APP,
                $widgetName,
                $widgetDefinitionConfig
            );
        }

        $routeNodes = $this->routeCollectionLoader->loadRouteCollection(
            LibraryInterface::APP,
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
            $widgetDefinitionNodes,
            $routeNodes,
            $homeNode,
            $pageViewNodes,
            $overlayViewNodes
        );
    }
}
