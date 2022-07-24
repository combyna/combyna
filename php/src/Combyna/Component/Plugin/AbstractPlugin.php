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
use LogicException;
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
     * {@inheritdoc]
     */
    public function getSubPlugins()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        /** @var YamlParser $yamlParser */
        $yamlParser = $container->get(static::YAML_PARSER_SERVICE);
        $libraryCollectionServiceDefinition = $container->getDefinition(static::PLUGIN_LIBRARY_COLLECTION_SERVICE);

        $librariesPath = $this->getDirectory() . '/Resources/config/libraries';

        if (is_dir($librariesPath)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator(
                    $librariesPath,
                    RecursiveDirectoryIterator::SKIP_DOTS
                )
            );

            foreach ($files as $path => $file) {
                /** @var SplFileInfo $file */
                $libraryConfig = $yamlParser->parse(file_get_contents($file->getPathname()));

                $libraryName = $file->getBasename('.yml');

                if (!isset($libraryConfig['name'])) {
                    throw new LogicException(sprintf(
                        'Missing "name" value for library "%s" in file "%s"',
                        $libraryName,
                        $file->getPathname()
                    ));
                }

                if ($libraryConfig['name'] !== $libraryName) {
                    throw new LogicException(sprintf(
                        'Mismatched "name" value for library "%s" in file "%s", "%s" given',
                        $libraryName,
                        $file->getPathname(),
                        $libraryConfig['name']
                    ));
                }

                $libraryCollectionServiceDefinition->addMethodCall('addLibraryConfig', [$libraryName, $libraryConfig]);
            }
        }
    }
}
