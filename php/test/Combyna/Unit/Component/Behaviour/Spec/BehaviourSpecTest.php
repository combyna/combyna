<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Behaviour\Spec;

use Combyna\Component\Behaviour\Node\StructuredNodeInterface;
use Combyna\Component\Behaviour\Query\Specifier\QuerySpecifierInterface;
use Combyna\Component\Behaviour\Spec\BehaviourSpec;
use Combyna\Component\Behaviour\Spec\BehaviourSpecInterface;
use Combyna\Component\Validator\Constraint\ConstraintInterface;
use Combyna\Component\Validator\Context\Factory\DelegatingSubValidationContextFactoryInterface;
use Combyna\Component\Validator\Context\Specifier\SubValidationContextSpecifierInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BehaviourSpecTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BehaviourSpecTest extends TestCase
{
    /**
     * @var ObjectProphecy|BehaviourSpecInterface
     */
    private $childSpec1;

    /**
     * @var ObjectProphecy|BehaviourSpecInterface
     */
    private $childSpec2;

    /**
     * @var ObjectProphecy|ConstraintInterface
     */
    private $constraint1;

    /**
     * @var ObjectProphecy|ConstraintInterface
     */
    private $constraint2;

    /**
     * @var ObjectProphecy|StructuredNodeInterface
     */
    private $node;

    /**
     * @var BehaviourSpec
     */
    private $spec;

    /**
     * @var ObjectProphecy|DelegatingSubValidationContextFactoryInterface
     */
    private $subValidationContextFactory;

    /**
     * @var ObjectProphecy|SubValidationContextSpecifierInterface
     */
    private $subValidationContextSpecifier;

    public function setUp()
    {
        $this->childSpec1 = $this->prophesize(BehaviourSpecInterface::class);
        $this->childSpec2 = $this->prophesize(BehaviourSpecInterface::class);
        $this->constraint1 = $this->prophesize(ConstraintInterface::class);
        $this->constraint2 = $this->prophesize(ConstraintInterface::class);
        $this->node = $this->prophesize(StructuredNodeInterface::class);
        $this->subValidationContextFactory = $this->prophesize(DelegatingSubValidationContextFactoryInterface::class);
        $this->subValidationContextSpecifier = $this->prophesize(SubValidationContextSpecifierInterface::class);

        $this->spec = new BehaviourSpec(
            $this->node->reveal(),
            [$this->constraint1->reveal(), $this->constraint2->reveal()],
            [$this->childSpec1->reveal(), $this->childSpec2->reveal()],
            $this->subValidationContextFactory->reveal(),
            $this->subValidationContextSpecifier->reveal()
        );
    }

    public function testGetDescendantSpecsWithQueryReturnsTheCurrentSpecWhenNodeMakesQueryDirectly()
    {
        $querySpecifier = $this->prophesize(QuerySpecifierInterface::class);
        $this->childSpec1->getDescendantSpecsWithQuery($querySpecifier)->willReturn([]);
        $this->childSpec2->getDescendantSpecsWithQuery($querySpecifier)->willReturn([]);
        $this->constraint1->makesQuery($querySpecifier)->willReturn(false);
        $this->constraint2->makesQuery($querySpecifier)->willReturn(false);
        $this->node->makesQuery($querySpecifier)->willReturn(true);

        $specs = $this->spec->getDescendantSpecsWithQuery($querySpecifier->reveal());

        self::assertSame([$this->spec], $specs);
    }

    public function testGetDescendantSpecsWithQueryReturnsTheCurrentSpecWhenOnlyAConstraintMakesQuery()
    {
        $querySpecifier = $this->prophesize(QuerySpecifierInterface::class);
        $this->childSpec1->getDescendantSpecsWithQuery($querySpecifier)->willReturn([]);
        $this->childSpec2->getDescendantSpecsWithQuery($querySpecifier)->willReturn([]);
        $this->constraint1->makesQuery($querySpecifier)->willReturn(false);
        $this->constraint2->makesQuery($querySpecifier)->willReturn(true);
        $this->node->makesQuery($querySpecifier)->willReturn(false);

        $specs = $this->spec->getDescendantSpecsWithQuery($querySpecifier->reveal());

        self::assertSame([$this->spec], $specs);
    }
}
