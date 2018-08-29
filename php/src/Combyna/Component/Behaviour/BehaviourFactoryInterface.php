<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilderInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Behaviour\Spec\SubBehaviourSpecBuilderInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;

/**
 * Interface BehaviourFactoryInterface
 *
 * Creates behaviour specifications and related objects
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourFactoryInterface
{
    /**
     * Creates a BehaviourSpec
     *
     * @param StructuredNodeInterface $node
     * @param ConstraintInterface[] $constraints
     * @param BehaviourSpecInterface[] $childSpecs
     * @param SubValidationContextSpecifierInterface $subValidationContextSpecifier
     * @return BehaviourSpecInterface
     */
    public function createBehaviourSpec(
        StructuredNodeInterface $node,
        array $constraints,
        array $childSpecs,
        SubValidationContextSpecifierInterface $subValidationContextSpecifier
    );

    /**
     * Creates a BehaviourSpecBuilder
     *
     * @param StructuredNodeInterface $node
     * @return BehaviourSpecBuilderInterface
     */
    public function createBehaviourSpecBuilder(StructuredNodeInterface $node);

    /**
     * Creates a BehaviourSpec
     *
     * @param StructuredNodeInterface $owningNode
     * @param ConstraintInterface[] $constraints
     * @param BehaviourSpecInterface[] $childSpecs
     * @param SubValidationContextSpecifierInterface $subValidationContextSpecifier
     * @return BehaviourSpecInterface
     */
    public function createSubBehaviourSpec(
        StructuredNodeInterface $owningNode,
        array $constraints,
        array $childSpecs,
        SubValidationContextSpecifierInterface $subValidationContextSpecifier
    );

    /**
     * Creates a SubBehaviourSpecBuilder
     *
     * @param StructuredNodeInterface $owningNode
     * @return SubBehaviourSpecBuilderInterface
     */
    public function createSubBehaviourSpecBuilder(StructuredNodeInterface $owningNode);
}
