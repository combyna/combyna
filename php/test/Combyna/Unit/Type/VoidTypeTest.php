<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Type;

use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Type\VoidType;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class VoidTypeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class VoidTypeTest extends TestCase
{
    /**
     * @var VoidType
     */
    private $type;

    public function setUp()
    {
        $this->type = new VoidType('my context');
    }

    public function testAllowsMultipleTypeReturnsTrueIfAllItsSubTypesAreAllowedByUs()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType1 */
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType2 */
        $theirSubType2 = $this->prophesize(TypeInterface::class);
        $theirSubType1->isAllowedByVoidType(Argument::is($this->type))->willReturn(true);
        $theirSubType2->isAllowedByVoidType(Argument::is($this->type))->willReturn(true);

        $this->assert(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        )->isTrue;
    }

    public function testAllowsMultipleTypeReturnsFalseIfOneOfItsSubTypesIsNotAllowedByUs()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType1 */
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType2 */
        $theirSubType2 = $this->prophesize(TypeInterface::class);
        $theirSubType1->isAllowedByVoidType(Argument::is($this->type))->willReturn(true);
        $theirSubType2->isAllowedByVoidType(Argument::is($this->type))->willReturn(false);

        $this->assert(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        )->isFalse;
    }

    public function testAllowsStaticAlwaysReturnsFalse()
    {
        /** @var ObjectProphecy|StaticInterface $static */
        $static = $this->prophesize(StaticInterface::class);

        $this->assert($this->type->allowsStatic($static->reveal()))->isFalse;
    }

    public function testAllowsStaticListTypeAlwaysReturnsFalse()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $theirElementType */
        $theirElementType = $this->prophesize(TypeInterface::class);

        $this->assert(
            $this->type->allowsStaticListType($candidateType->reveal(), $theirElementType->reveal())
        )->isFalse;
    }

    public function testAllowsStaticTypeAlwaysReturnsFalse()
    {
        $candidateType = new StaticType(StaticInterface::class);

        $this->assert($this->type->allowsStaticType($candidateType))->isFalse;
    }

    public function testGetSummaryReturnsTheCorrectString()
    {
        $this->assert($this->type->getSummary())->exactlyEquals('void<my context>');
    }

    public function testMergeWithMultipleTypeJustReturnsTheMultipleType()
    {
        /** @var ObjectProphecy|MultipleType $otherType */
        $otherType = $this->prophesize(MultipleType::class);
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        $theirSubType2 = $this->prophesize(TypeInterface::class);
        $theirSubType1->getSummary()->willReturn('their-sub-type-1');
        $theirSubType2->getSummary()->willReturn('their-sub-type-2');

        $result = $this->type->mergeWithMultipleType($otherType->reveal(), [
            $theirSubType1->reveal(),
            $theirSubType2->reveal()
        ]);

        $this->assert($result)->exactlyEquals($otherType->reveal());
    }

    public function testMergeWithStaticListTypeJustReturnsTheStaticList()
    {
        /** @var ObjectProphecy|StaticListType $otherType */
        $otherType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        $result = $this->type->mergeWithStaticListType($otherType->reveal(), $elementType->reveal());

        $this->assert($result)->exactlyEquals($otherType->reveal());
    }

    public function testMergeWithStaticTypeJustReturnsTheStaticType()
    {
        $staticType = new StaticType(StaticInterface::class);

        $result = $this->type->mergeWithStaticType($staticType);

        $this->assert($result)->exactlyEquals($staticType);
    }
}
