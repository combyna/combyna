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
use Combyna\Component\Behaviour\Spec\BehaviourSpec;
use Combyna\Component\Behaviour\Spec\BehaviourSpecBuilder;
use Combyna\Component\Behaviour\Spec\SubBehaviourSpec;
use Combyna\Component\Behaviour\Spec\SubBehaviourSpecBuilder;
use Combyna\Component\Validator\Context\Factory\DelegatingSubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;

/**
 * Class BehaviourFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BehaviourFactory implements BehaviourFactoryInterface
{
    /**
     * @var DelegatingSubValidationContextFactoryInterface
     */
    private $subValidationContextFactory;

    /**
     * @param DelegatingSubValidationContextFactoryInterface $subValidationContextFactory
     */
    public function __construct(
        DelegatingSubValidationContextFactoryInterface $subValidationContextFactory
    ) {
        $this->subValidationContextFactory = $subValidationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createBehaviourSpec(
        StructuredNodeInterface $node,
        array $constraints,
        array $childSpecs,
        SubValidationContextSpecifierInterface $subValidationContextSpecifier
    ) {
        return new BehaviourSpec(
            $node,
            $constraints,
            $childSpecs,
            $this->subValidationContextFactory,
            $subValidationContextSpecifier
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createBehaviourSpecBuilder(StructuredNodeInterface $node)
    {
        return new BehaviourSpecBuilder($this, $node);
    }

    /**
     * {@inheritdoc}
     */
    public function createSubBehaviourSpec(
        StructuredNodeInterface $owningNode,
        array $constraints,
        array $childSpecs,
        SubValidationContextSpecifierInterface $subValidationContextSpecifier
    ) {
        return new SubBehaviourSpec(
            $owningNode,
            $constraints,
            $childSpecs,
            $this->subValidationContextFactory,
            $subValidationContextSpecifier
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createSubBehaviourSpecBuilder(StructuredNodeInterface $owningNode)
    {
        return new SubBehaviourSpecBuilder(
            $this,
            $this->subValidationContextFactory,
            $owningNode
        );
    }
}
