<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Instruction\DependencyInjection;

use Combyna\Component\Common\AbstractComponentExtension;
use Combyna\Component\Framework\DelegatorInitialiser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class InstructionExtension
 *
 * Serves as both a container extension and compiler pass.
 * The extension is used to load the config for this component into the container,
 * while the compiler pass is used to find any tagged instruction loader services and add them to the delegator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InstructionExtension extends AbstractComponentExtension implements CompilerPassInterface
{
    const LOADER_DELEGATOR_SERVICE_ID = 'combyna.instruction.loader';
    const LOADER_DELEGATEE_TAG = 'combyna.instruction_loader';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $this->processTaggedLoaders($containerBuilder);
    }

    /**
     * Processes all services tagged as instruction loaders
     *
     * @param ContainerBuilder $containerBuilder
     */
    private function processTaggedLoaders(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::LOADER_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the instruction loader tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::LOADER_DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the instruction loader service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::LOADER_DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addLoader'
            ]);
        }
    }
}
