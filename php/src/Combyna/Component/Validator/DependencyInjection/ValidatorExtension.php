<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\DependencyInjection;

use Combyna\Component\Common\AbstractComponentExtension;
use Combyna\Component\Framework\DelegatorInitialiser;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ValidatorExtension
 *
 * Serves as both a container extension and compiler pass.
 * The extension is used to load the config for this component into the container,
 * while the compiler pass is used to find any relevant tagged services and add them to the delegator
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidatorExtension extends AbstractComponentExtension implements CompilerPassInterface
{
    const CONSTRAINT_VALIDATOR_DELEGATOR_SERVICE_ID = 'combyna.validator.delegating_constraint_validator';
    const CONSTRAINT_VALIDATOR_DELEGATEE_TAG = 'combyna.validation_constraint_validator';

    const SUB_VALIDATION_CONTEXT_FACTORY_DELEGATOR_SERVICE_ID = 'combyna.validator.delegating_sub_validation_context_factory';
    const SUB_VALIDATION_CONTEXT_FACTORY_DELEGATEE_TAG = 'combyna.sub_validation_context_factory';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $containerBuilder)
    {
        $this->processConstraintValidators($containerBuilder);
        $this->processSubValidationContextFactories($containerBuilder);
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    private function processConstraintValidators(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::CONSTRAINT_VALIDATOR_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the constraint validator tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::CONSTRAINT_VALIDATOR_DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the constraint validator service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::CONSTRAINT_VALIDATOR_DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addConstraintValidator'
            ]);
        }
    }

    /**
     * @param ContainerBuilder $containerBuilder
     */
    private function processSubValidationContextFactories(ContainerBuilder $containerBuilder)
    {
        if (!$containerBuilder->has(self::SUB_VALIDATION_CONTEXT_FACTORY_DELEGATOR_SERVICE_ID)) {
            return;
        }

        $initialiserDefinition = $containerBuilder->findDefinition(DelegatorInitialiser::SERVICE_ID);

        // Find all service IDs with the sub-validation context factory tag
        $taggedServices = $containerBuilder->findTaggedServiceIds(self::SUB_VALIDATION_CONTEXT_FACTORY_DELEGATEE_TAG);

        foreach ($taggedServices as $id => $tags) {
            // Add the sub-validation context factory service to the delegator
            $initialiserDefinition->addMethodCall('addDelegatee', [
                new Reference(self::SUB_VALIDATION_CONTEXT_FACTORY_DELEGATOR_SERVICE_ID),
                new Reference($id),
                'addFactory'
            ]);
        }
    }
}
