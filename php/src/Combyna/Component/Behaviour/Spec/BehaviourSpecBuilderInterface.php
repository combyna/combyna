<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Behaviour\Spec;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;

/**
 * Interface BehaviourSpecBuilderInterface
 *
 * Builds a BehaviourSpec for the specified validatable tree node to use
 * to build the specification for how it should behave and be validated
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourSpecBuilderInterface
{
    /**
     * Adds a child node to be recursed into (eg. for validation)
     *
     * @param StructuredNodeInterface $childNode
     */
    public function addChildNode(StructuredNodeInterface $childNode);

    /**
     * Adds a constraint to be validated for the node this builder is for
     *
     * @param ConstraintInterface $constraint
     */
    public function addConstraint(ConstraintInterface $constraint);

    /**
     * Adds a modifier to be run before the spec is built
     *
     * @param BehaviourSpecModifierInterface $specModifier
     */
    public function addModifier(BehaviourSpecModifierInterface $specModifier);

    /**
     * Adds a sub-spec to be built by the provided callback.
     * Used for creating nested validation contexts within one ACT node.
     *
     * @param callable $builder
     */
    public function addSubSpec(callable $builder);

    /**
     * Creates a BehaviourSpec from the state of this builder
     *
     * @return BehaviourSpecInterface
     */
    public function build();

    /**
     * Allows a custom validation context to be created for validating the current node
     * and its descendants
     *
     * @param SubValidationContextSpecifierInterface|null $specifier
     */
    public function defineValidationContext(SubValidationContextSpecifierInterface $specifier = null);
}
