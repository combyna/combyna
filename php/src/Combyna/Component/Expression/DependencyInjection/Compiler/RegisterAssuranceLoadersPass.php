<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\DependencyInjection\Compiler;

use Combyna\Component\Framework\DelegatorInitialiser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RegisterAssuranceLoadersPass
 *
 * Compiler pass to automatically handle services tagged as guard assurance loaders
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterAssuranceLoadersPass implements CompilerPassInterface
{
    const LOADER_DELEGATOR_SERVICE_ID = 'combyna.expression.loader.assurance';
    const LOADER_DELEGATEE_TAG = 'combyna.assurance_loader';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::LOADER_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the loader tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::LOADER_DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the sub-loader service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::LOADER_DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addLoader'
            ]);
        }
    }
}
