<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterNativeFunctionProvidersPass
 *
 * Compiler pass to automatically handle services tagged as native library function providers
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterNativeFunctionProvidersPass implements CompilerPassInterface
{
    const INSTALLER_LISTENER_SERVICE_ID = 'combyna.environment.event_listener.native_function_installer';
    const NATIVE_FUNCTION_TAG = 'combyna.native_library_function_provider';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::INSTALLER_LISTENER_SERVICE_ID)) {
            return;
        }

        $installerDefinition = $containerBuilder->findDefinition(self::INSTALLER_LISTENER_SERVICE_ID);

        // Find all service IDs with the native library function tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::NATIVE_FUNCTION_TAG);

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                $installerDefinition->addMethodCall('addProvider', [
                    new Reference($id)
                ]);
            }
        }
    }
}
