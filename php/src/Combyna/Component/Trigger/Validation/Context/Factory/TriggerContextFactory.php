<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Validation\Context\Factory;

use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNode;
use Combyna\Component\Trigger\Validation\Context\Specifier\TriggerContextSpecifier;
use Combyna\Component\Trigger\Validation\Context\TriggerSubValidationContext;
use Combyna\Component\Trigger\Validation\Context\TriggerSubValidationContextInterface;
use Combyna\Component\Validator\Context\Factory\SubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class TriggerContextFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerContextFactory implements SubValidationContextFactoryInterface
{
    /**
     * Creates a TriggerSubValidationContext
     *
     * @param TriggerContextSpecifier $specifier
     * @param SubValidationContextInterface $parentContext
     * @param TriggerNode $triggerNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @return TriggerSubValidationContextInterface
     */
    public function createTriggerContext(
        TriggerContextSpecifier $specifier,
        SubValidationContextInterface $parentContext,
        TriggerNode $triggerNode,
        BehaviourSpecInterface $behaviourSpec
    ) {
        return new TriggerSubValidationContext(
            $parentContext,
            $triggerNode,
            $behaviourSpec
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSpecifierClassToContextFactoryCallableMap()
    {
        return [
            TriggerContextSpecifier::class => [$this, 'createTriggerContext']
        ];
    }
}
