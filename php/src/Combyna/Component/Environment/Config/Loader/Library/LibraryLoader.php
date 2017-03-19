<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Config\Loader\Library;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Environment\Config\Act\LibraryNode;
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
     * @var WidgetDefinitionLoaderInterface
     */
    private $widgetDefinitionLoader;

    /**
     * @param ConfigParser $configParser
     * @param WidgetDefinitionLoaderInterface $widgetDefinitionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        WidgetDefinitionLoaderInterface $widgetDefinitionLoader
    ) {
        $this->configParser = $configParser;
        $this->widgetDefinitionLoader = $widgetDefinitionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function loadLibrary(array $libraryConfig)
    {
        $libraryName = $this->configParser->getElement($libraryConfig, 'name', 'library name');
        $widgetDefinitionConfigs = $this->configParser->getElement($libraryConfig, 'widgets', 'widget definitions');

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
            [], // TODO: FunctionLoader
            $widgetDefinitionNodes
        );
    }
}
