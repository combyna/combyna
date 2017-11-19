<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\DependencyInjection;

use Combyna\Component\Common\AbstractComponentExtension;
use Combyna\Component\Framework\DelegatorInitialiser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ExpressionExtension
 *
 * Serves as both a container extension and compiler pass.
 * The extension is used to load the config for this component into the container,
 * while the compiler pass is used to find any tagged loader services and add them to the delegator
 * with the same done for any tagged promoter services
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionExtension extends AbstractComponentExtension implements CompilerPassInterface
{
    const BUILTIN_LOADER_DELEGATOR_SERVICE_ID = 'combyna.expression.loader.builtin';
    const BUILTIN_LOADER_DELEGATEE_TAG = 'combyna.builtin_expression_loader';

    const LOADER_DELEGATOR_SERVICE_ID = 'combyna.expression.loader';
    const LOADER_DELEGATEE_TAG = 'combyna.expression_loader';

    const PROMOTER_DELEGATOR_SERVICE_ID = 'combyna.expression.act.promoter';
    const PROMOTER_DELEGATEE_TAG = 'combyna.expression_promoter';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->processTaggedLoaders($container);
        $this->processTaggedBuiltinLoaders($container);
        $this->processTaggedPromoters($container);
    }

    /**
     * Processes all services tagged as builtin expression loaders
     *
     * @param ContainerBuilder $containerBuilder
     */
    private function processTaggedBuiltinLoaders(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::BUILTIN_LOADER_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the expression loader tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::BUILTIN_LOADER_DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the sub-builtin-loader service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::BUILTIN_LOADER_DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addBuiltinLoader'
            ]);
        }
    }

    /**
     * Processes all services tagged as expression loaders
     *
     * @param ContainerBuilder $containerBuilder
     */
    private function processTaggedLoaders(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::LOADER_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the expression loader tag
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

    /**
     * Processes all services tagged as expression promoters
     *
     * @param ContainerBuilder $containerBuilder
     */
    private function processTaggedPromoters(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::PROMOTER_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the expression promoter tag
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
