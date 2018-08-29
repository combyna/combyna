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
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Interface BehaviourSpecInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface BehaviourSpecInterface
{
    /**
     * Fetches the classes of all constraints used by descendants of this behaviour spec's structured node
     *
     * @return string[]
     */
    public function getAllConstraintClassesUsed();

    /**
     * Fetches the child specs to be validated recursively
     *
     * @return BehaviourSpecInterface[]
     */
    public function getChildSpecs();

    /**
     * Fetches the constraints that will be applied by the validation spec
     *
     * @return ConstraintInterface[]
     */
    public function getConstraints();

    /**
     * Fetches the behaviour spec for the given node, or null if the node is not a descendant of this one
     *
     * @param StructuredNodeInterface $node
     * @return BehaviourSpecInterface|null
     */
    public function getDescendantSpecForNode(StructuredNodeInterface $node);

    /**
     * Fetches all descendant specs that match the specified query
     *
     * @param QuerySpecifierInterface $querySpecifier
     * @return BehaviourSpecInterface[]
     */
    public function getDescendantSpecsWithQuery(QuerySpecifierInterface $querySpecifier);

    /**
     * Fetches the node that owns the subject of this spec
     *
     * @return StructuredNodeInterface
     */
    public function getSubjectOwnerNode();

    /**
     * Builds a tree of sub-validation contexts for the current structured node
     *
     * @param SubValidationContextInterface $parentContext
     * @return SubValidationContextInterface|null
     */
    public function getSubValidationContext(SubValidationContextInterface $parentContext);

    /**
     * Builds a tree of sub-validation contexts up to the specified structured node, if found
     *
     * @param StructuredNodeInterface $structuredNode
     * @param SubValidationContextInterface $parentContext
     * @return SubValidationContextInterface|null
     */
    public function getSubValidationContextForDescendant(
        StructuredNodeInterface $structuredNode,
        SubValidationContextInterface $parentContext
    );
}
