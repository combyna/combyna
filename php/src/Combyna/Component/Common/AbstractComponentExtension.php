<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Common;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Class AbstractComponentExtension
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractComponentExtension extends Extension implements ComponentExtensionInterface
{
    /**
     * @var ComponentInterface
     */
    private $component;

    /**
     * @param ComponentInterface $component
     */
    public function __construct(ComponentInterface $component)
    {
        $this->component = $component;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
        $configDirPath = $this->component->getDirectory() . '/Resources/config';

        if (!is_dir($configDirPath . '/services')) {
            // Component does not have any service definition config files
            return;
        }

        $fileLocator = new FileLocator($configDirPath);
        $loader = new DirectoryLoader($containerBuilder, $fileLocator);
        $loader->setResolver(new LoaderResolver([
            new YamlFileLoader($containerBuilder, $fileLocator),
            $loader
        ]));
        $loader->load('services/');
    }
}
