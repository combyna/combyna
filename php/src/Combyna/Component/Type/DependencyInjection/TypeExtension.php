<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Type\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TypeExtension
 *
 * Serves as both a container extension and compiler pass.
 * The extension is used to load the config for this component into the container,
 * while the compiler pass is used to find any tagged type loader services and add them to the delegator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TypeExtension extends Extension implements CompilerPassInterface
{
    const DELEGATOR_SERVICE_ID = 'combyna.type.loader';

    const DELEGATEE_TAG = 'combyna.type_loader';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
        $fileLocator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new DirectoryLoader($containerBuilder, $fileLocator);
        $loader->setResolver(new LoaderResolver([
            new YamlFileLoader($containerBuilder, $fileLocator),
            $loader
        ]));
        $loader->load('services/');
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::DELEGATOR_SERVICE_ID)) {
            return;
        }

        $definition = $containerBuilder->findDefinition(self::DELEGATOR_SERVICE_ID);

        // Find all service IDs with the type loader tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the type loader service to the delegating service
            $definition->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}
