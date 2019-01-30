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
 * Class RegisterAssuranceNodePromotersPass
 *
 * Compiler pass to automatically handle services tagged as guard assurance promoters
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterAssuranceNodePromotersPass implements CompilerPassInterface
{
    const PROMOTER_DELEGATOR_SERVICE_ID = 'combyna.expression.act.assurance_promoter';
    const PROMOTER_DELEGATEE_TAG = 'combyna.assurance_promoter';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::PROMOTER_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the promoter tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::PROMOTER_DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the sub-promoter service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::PROMOTER_DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addPromoter'
            ]);
        }
    }
}
