<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Loader\Library;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Config\Act\LibraryNode;
use Combyna\Component\Environment\Config\Loader\Functions\FunctionLoaderInterface;
use Combyna\Component\Event\Config\Loader\EventDefinitionLoaderInterface;
use Combyna\Component\Signal\Config\Loader\SignalDefinitionLoaderInterface;
use Combyna\Component\Ui\Config\Loader\WidgetDefinitionLoaderInterface;

/**
 * Class LibraryLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class LibraryLoader implements LibraryLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var EventDefinitionLoaderInterface
     */
    private $eventDefinitionLoader;

    /**
     * @var FunctionLoaderInterface
     */
    private $functionLoader;

    /**
     * @var SignalDefinitionLoaderInterface
     */
    private $signalDefinitionLoader;

    /**
     * @var WidgetDefinitionLoaderInterface
     */
    private $widgetDefinitionLoader;

    /**
     * @param ConfigParser $configParser
     * @param EventDefinitionLoaderInterface $eventDefinitionLoader
     * @param FunctionLoaderInterface $functionLoader
     * @param SignalDefinitionLoaderInterface $signalDefinitionLoader
     * @param WidgetDefinitionLoaderInterface $widgetDefinitionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        EventDefinitionLoaderInterface $eventDefinitionLoader,
        FunctionLoaderInterface $functionLoader,
        SignalDefinitionLoaderInterface $signalDefinitionLoader,
        WidgetDefinitionLoaderInterface $widgetDefinitionLoader
    ) {
        $this->configParser = $configParser;
        $this->eventDefinitionLoader = $eventDefinitionLoader;
        $this->functionLoader = $functionLoader;
        $this->signalDefinitionLoader = $signalDefinitionLoader;
        $this->widgetDefinitionLoader = $widgetDefinitionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadLibrary(array $libraryConfig)
    {
        $libraryName = $this->configParser->getElement($libraryConfig, 'name', 'library name');
        $description = $this->configParser->getElement($libraryConfig, 'description', 'library description');
        $libraryNamesDependedOn = $this->configParser->getOptionalElement($libraryConfig, 'dependencies', 'library dependencies', [], 'array');
        $eventDefinitionConfigs = $this->configParser->getOptionalElement($libraryConfig, 'events', 'event definitions', [], 'array');
        $functionConfigs = $this->configParser->getOptionalElement($libraryConfig, 'functions', 'functions', [], 'array');
        $signalDefinitionConfigs = $this->configParser->getOptionalElement($libraryConfig, 'signals', 'signal definitions', [], 'array');
        $widgetDefinitionConfigs = $this->configParser->getOptionalElement($libraryConfig, 'widgets', 'widget definitions', [], 'array');

        $eventDefinitionNodes = [];

        foreach ($eventDefinitionConfigs as $eventName => $eventDefinitionConfig) {
            $eventDefinitionNodes[] = $this->eventDefinitionLoader->load(
                $libraryName,
                $eventName,
                $eventDefinitionConfig
            );
        }

        $functionNodes = [];

        foreach ($functionConfigs as $functionName => $functionConfig) {
            $functionNodes[] = $this->functionLoader->load(
                $libraryName,
                $functionName,
                $functionConfig
            );
        }

        $signalDefinitionNodes = [];

        foreach ($signalDefinitionConfigs as $signalName => $signalDefinitionConfig) {
            $signalDefinitionNodes[] = $this->signalDefinitionLoader->load(
                $libraryName,
                $signalName,
                $signalDefinitionConfig
            );
        }

        $widgetDefinitionNodes = [];

        foreach ($widgetDefinitionConfigs as $widgetDefinitionName => $widgetDefinitionConfig) {
            $widgetDefinitionNodes[] = $this->widgetDefinitionLoader->load(
                $libraryName,
                $widgetDefinitionName,
                $widgetDefinitionConfig
            );
        }

        return new LibraryNode(
            $libraryName,
            $description,
            $libraryNamesDependedOn,
            $functionNodes,
            $eventDefinitionNodes,
            $signalDefinitionNodes,
            $widgetDefinitionNodes
        );
    }
}
