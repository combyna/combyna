<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Validator\Context\Factory;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface DelegatingSubValidationContextFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DelegatingSubValidationContextFactoryInterface
{
    /**
     * Registers a validator for a type of constraint
     *
     * @param SubValidationContextFactoryInterface $contextFactory
     */
    public function addFactory(SubValidationContextFactoryInterface $contextFactory);

    /**
     * Creates a SubValidationContext
     *
     * @param SubValidationContextSpecifierInterface $contextSpecifier
     * @param SubValidationContextInterface $parentContext
     * @param StructuredNodeInterface $structuredNode
     * @param BehaviourSpecInterface $behaviourSpec
     * @param StructuredNodeInterface $subjectNode
     * @return SubValidationContextInterface
     */
    public function createContext(
        SubValidationContextSpecifierInterface $contextSpecifier,
        SubValidationContextInterface $parentContext,
        StructuredNodeInterface $structuredNode,
        BehaviourSpecInterface $behaviourSpec,
        StructuredNodeInterface $subjectNode
    );
}
