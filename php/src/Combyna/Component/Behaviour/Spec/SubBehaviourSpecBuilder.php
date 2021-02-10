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

use Combyna\Component\Behaviour\BehaviourFactoryInterface;
use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;
use Combyna\Component\Validator\Context\Factory\DelegatingSubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\Specifier\ActNodeContextSpecifier;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;

/**
 * Class SubBehaviourSpecBuilder
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SubBehaviourSpecBuilder implements SubBehaviourSpecBuilderInterface
{
    /**
     * @var BehaviourFactoryInterface
     */
    private $behaviourFactory;

    /**
     * @var StructuredNodeInterface[]
     */
    private $childNodes = [];

    /**
     * @var ConstraintInterface[]
     */
    private $constraints = [];

    /**
     * @var StructuredNodeInterface
     */
    private $owningNode;

    /**
     * @var BehaviourSpecModifierInterface[]
     */
    private $specModifiers = [];

    /**
     * @var callable[]
     */
    private $subSpecBuilders = [];

    /**
     * @var DelegatingSubValidationContextFactoryInterface
     */
    private $subValidationContextFactory;

    /**
     * @var SubValidationContextSpecifierInterface|null
     */
    private $subValidationContextSpecifier = null;

    /**
     * @param BehaviourFactoryInterface $behaviourFactory
     * @param DelegatingSubValidationContextFactoryInterface $subValidationContextFactory
     * @param StructuredNodeInterface $owningNode
     */
    public function __construct(
        BehaviourFactoryInterface $behaviourFactory,
        DelegatingSubValidationContextFactoryInterface $subValidationContextFactory,
        StructuredNodeInterface $owningNode
    ) {
        $this->behaviourFactory = $behaviourFactory;
        $this->owningNode = $owningNode;
        $this->subValidationContextFactory = $subValidationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function addChildNode(StructuredNodeInterface $childNode)
    {
        $this->childNodes[] = $childNode;
    }

    /**
     * {@inheritdoc}
     */
    public function addConstraint(ConstraintInterface $constraint)
    {
        $this->constraints[] = $constraint;
    }

    /**
     * {@inheritdoc}
     */
    public function addModifier(BehaviourSpecModifierInterface $specModifier)
    {
        $this->specModifiers[] = $specModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function addSubSpec(callable $builder)
    {
        $this->subSpecBuilders[] = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function build()
    {
        // Allow any modifiers to modify the spec before continuing
        foreach ($this->specModifiers as $specModifier) {
            $specModifier->modifySpecBuilder($this);
        }

        $childSpecs = array_map(function (StructuredNodeInterface $childNode) {
            $childSpecBuilder = $this->behaviourFactory->createBehaviourSpecBuilder($childNode);

            $childNode->buildBehaviourSpec($childSpecBuilder);

            return $childSpecBuilder->build();
        }, $this->childNodes);

        $subSpecs = array_map(function (callable $buildSubSpec) {
            $subSpecBuilder = $this->behaviourFactory->createSubBehaviourSpecBuilder($this->owningNode);

            $buildSubSpec($subSpecBuilder);

            return $subSpecBuilder->build();
        }, $this->subSpecBuilders);

        $subValidationContextSpecifier = $this->subValidationContextSpecifier;

        if ($subValidationContextSpecifier === null) {
            // TODO: No need to nest another ACT node context - add NullNodeContextSpecifier?
            $subValidationContextSpecifier = new ActNodeContextSpecifier();
        }

        return $this->behaviourFactory->createSubBehaviourSpec(
            $this->owningNode,
            $this->constraints,
            array_merge($childSpecs, $subSpecs),
            $subValidationContextSpecifier
        );
    }

    /**
     * {@inheritdoc}
     */
    public function defineValidationContext(SubValidationContextSpecifierInterface $specifier = null)
    {
        $this->subValidationContextSpecifier = $specifier;
    }
}
