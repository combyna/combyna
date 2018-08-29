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
use Combyna\Component\Validator\Context\Factory\DelegatingSubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Component\Validator\Context\SubValidationContextInterface;

/**
 * Class BehaviourSpec
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BehaviourSpec implements BehaviourSpecInterface
{
    /**
     * @var BehaviourSpecInterface[]
     */
    private $childSpecs;

    /**
     * @var ConstraintInterface[]
     */
    private $constraints;

    /**
     * @var StructuredNodeInterface
     */
    private $node;

    /**
     * @var DelegatingSubValidationContextFactoryInterface
     */
    private $subValidationContextFactory;

    /**
     * @var SubValidationContextSpecifierInterface
     */
    private $subValidationContextSpecifier;

    /**
     * @param StructuredNodeInterface $node
     * @param ConstraintInterface[] $constraints
     * @param BehaviourSpecInterface[] $childSpecs
     * @param DelegatingSubValidationContextFactoryInterface $subValidationContextFactory
     * @param SubValidationContextSpecifierInterface $subValidationContextSpecifier
     */
    public function __construct(
        StructuredNodeInterface $node,
        array $constraints,
        array $childSpecs,
        DelegatingSubValidationContextFactoryInterface $subValidationContextFactory,
        SubValidationContextSpecifierInterface $subValidationContextSpecifier
    ) {
        $this->childSpecs = $childSpecs;
        $this->constraints = $constraints;
        $this->node = $node;
        $this->subValidationContextFactory = $subValidationContextFactory;
        $this->subValidationContextSpecifier = $subValidationContextSpecifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllConstraintClassesUsed()
    {
        // Fetch classes of all constraints for this spec
        $constraintClasses = array_map(function (ConstraintInterface $constraint) {
            return get_class($constraint);
        }, $this->constraints);

        // Add on the classes of all constraints for the child specs
        foreach ($this->childSpecs as $childSpec) {
            $constraintClasses = array_merge($constraintClasses, $childSpec->getAllConstraintClassesUsed());
        }

        return array_unique($constraintClasses);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildSpecs()
    {
        return $this->childSpecs;
    }

    /**
     * {@inheritdoc}
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantSpecForNode(StructuredNodeInterface $node)
    {
        if ($this->node === $node) {
            return $this;
        }

        foreach ($this->childSpecs as $childSpec) {
            $descendantSpec = $childSpec->getDescendantSpecForNode($node);

            if ($descendantSpec !== null) {
                return $descendantSpec;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescendantSpecsWithQuery(QuerySpecifierInterface $querySpecifier)
    {
        $matchingSpecs = [];

        if ($this->node->makesQuery($querySpecifier)) {
            $matchingSpecs[] = $this;
        }

        foreach ($this->childSpecs as $childSpec) {
            $matchingSpecs = array_merge($matchingSpecs, $childSpec->getDescendantSpecsWithQuery($querySpecifier));
        }

        return $matchingSpecs;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubjectOwnerNode()
    {
        return $this->node;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubValidationContext(SubValidationContextInterface $parentContext)
    {
        return $this->getSubValidationContextForDescendant(
            $this->node,
            $parentContext
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSubValidationContextForDescendant(
        StructuredNodeInterface $structuredNode,
        SubValidationContextInterface $parentContext
    ) {
        $subValidationContext = $this->subValidationContextFactory->createContext(
            $this->subValidationContextSpecifier,
            $parentContext,
            $this->node,
            $this
        );

        if ($this->node === $structuredNode) {
            // This current spec is for the node we're looking for
            return $subValidationContext;
        }

        // Go through all the child specs of this one recursively for our branch of the tree
        foreach ($this->childSpecs as $childSpec) {
            $descendantValidationContext = $childSpec->getSubValidationContextForDescendant(
                $structuredNode,
                $subValidationContext
            );

            if ($descendantValidationContext) {
                return $descendantValidationContext;
            }
        }

        // Node is not anywhere in this branch of the spec tree
        return null;
    }
}
