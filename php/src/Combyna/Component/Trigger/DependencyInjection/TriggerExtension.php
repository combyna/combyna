<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\DependencyInjection;

use Combyna\Component\Common\AbstractComponentExtension;
use Combyna\Component\Framework\DelegatorInitialiser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TriggerExtension
 *
 * Serves as both a container extension and compiler pass.
 * The extension is used to load the config for this component into the container,
 * while the compiler pass is used to find any tagged instruction promoter services and add them to the delegator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerExtension extends AbstractComponentExtension implements CompilerPassInterface
{
    const INSTRUCTION_PROMOTER_DELEGATOR_SERVICE_ID = 'combyna.trigger.instruction.promoter';
    const INSTRUCTION_PROMOTER_DELEGATEE_TAG = 'combyna.trigger_instruction_promoter';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $this->processTaggedInstructionPromoters($containerBuilder);
    }

    /**
     * Processes all services tagged as instruction promoters
     *
     * @param ContainerBuilder $containerBuilder
     */
    private function processTaggedInstructionPromoters(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::INSTRUCTION_PROMOTER_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the instruction promoter tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::INSTRUCTION_PROMOTER_DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the instruction promoter service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::INSTRUCTION_PROMOTER_DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addPromoter'
            ]);
        }
    }
}
