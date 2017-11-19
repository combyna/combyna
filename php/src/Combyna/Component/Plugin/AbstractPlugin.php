<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Plugin;

use Combyna\Component\Common\AbstractComponent;
use Combyna\Component\Config\YamlParser;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class AbstractPlugin
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractPlugin extends AbstractComponent implements PluginInterface
{
    const PLUGIN_LIBRARY_COLLECTION_SERVICE = 'combyna.plugin.library_collection';
    const YAML_PARSER_SERVICE = 'combyna.config.yaml_parser';

    /**
     * {@inheritdoc]
     */
    public function getName()
    {
        preg_match('@([^\\\\]+)Plugin$@', static::class, $matches);

        return $matches[1];
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /** @var YamlParser $yamlParser */
        $yamlParser = $container->get(static::YAML_PARSER_SERVICE);
        $libraryCollectionServiceDefinition = $container->getDefinition(static::PLUGIN_LIBRARY_COLLECTION_SERVICE);

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(
                $this->getDirectory() . '/Resources/config/libraries',
                RecursiveDirectoryIterator::SKIP_DOTS
            )
        );

        foreach ($files as $path => $file) {
            /** @var SplFileInfo $file */
            $libraryConfig = $yamlParser->parse(file_get_contents($file->getPathname()));
            $libraryCollectionServiceDefinition->addMethodCall('addLibraryConfig', [$libraryConfig]);
        }
    }
}
