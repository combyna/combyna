<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Config\DependencyInjection;

use Combyna\Component\Common\AbstractComponentExtension;
use Combyna\Component\Framework\DelegatorInitialiser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ConfigExtension
 *
 * Serves as both a container extension and compiler pass.
 * The extension is used to load the config for this component into the container,
 * while the compiler pass is used to find any tagged node visitor services and add them to the delegator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConfigExtension extends AbstractComponentExtension implements CompilerPassInterface
{
    const DELEGATOR_SERVICE_ID = 'combyna.config.node_visitor';

    const DELEGATEE_TAG = 'combyna.config_node_visitor';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the node visitor tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the node visitor service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addVisitor'
            ]);
        }
    }
}
