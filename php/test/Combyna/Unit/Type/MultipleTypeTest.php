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

use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class MultipleTypeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class MultipleTypeTest extends TestCase
{
    /**
     * @var ObjectProphecy|TypeInterface
     */
    private $ourSubType1;

    /**
     * @var ObjectProphecy|TypeInterface
     */
    private $ourSubType2;

    /**
     * @var MultipleType
     */
    private $type;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->ourSubType1 = $this->prophesize(TypeInterface::class);
        $this->ourSubType2 = $this->prophesize(TypeInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->type = new MultipleType(
            [
                $this->ourSubType1->reveal(),
                $this->ourSubType2->reveal()
            ],
            $this->validationContext->reveal()
        );
    }

    public function testAllowsMultipleTypeReturnsTrueWhenFirstOfOurSubTypesAllowsAllTheirSubTypes()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        $theirSubType2 = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->allows(Argument::is($theirSubType1->reveal()))->willReturn(true);
        $this->ourSubType1->allows(Argument::is($theirSubType2->reveal()))->willReturn(true);

        static::assertTrue(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsMultipleTypeReturnsFalseWhenNeitherOfOurSubTypesAllowsOneOfTheirSubTypes()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        $theirSubType2 = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->allows(Argument::is($theirSubType1->reveal()))->willReturn(true);
        $this->ourSubType1->allows(Argument::is($theirSubType2->reveal()))->willReturn(false);
        $this->ourSubType2->allows(Argument::is($theirSubType2->reveal()))->willReturn(false);

        static::assertFalse(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsMultipleTypeReturnsTrueWhenEachOfOurSubTypesAllowsEachOfTheirSubTypesInOrder()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        $theirSubType2 = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->allows(Argument::is($theirSubType1->reveal()))->willReturn(true);
        $this->ourSubType1->allows(Argument::is($theirSubType2->reveal()))->willReturn(false);
        $this->ourSubType2->allows(Argument::is($theirSubType2->reveal()))->willReturn(true);

        static::assertTrue(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsMultipleTypeReturnsTrueWhenEachOfOurSubTypesAllowsEachOfTheirSubTypesOutOfOrder()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        $theirSubType2 = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->allows(Argument::is($theirSubType1->reveal()))->willReturn(false);
        $this->ourSubType1->allows(Argument::is($theirSubType2->reveal()))->willReturn(true);
        $this->ourSubType2->allows(Argument::is($theirSubType1->reveal()))->willReturn(true);
        $this->ourSubType2->allows(Argument::is($theirSubType2->reveal()))->willReturn(false);

        static::assertTrue(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsStaticListTypeReturnsTrueWhenOurFirstSubTypeAllowsTheListType()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->allows(Argument::is($candidateType->reveal()))->willReturn(true);
        $this->ourSubType2->allows(Argument::is($candidateType->reveal()))->willReturn(false);

        static::assertTrue(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        );
    }

    public function testAllowsStaticListTypeReturnsTrueWhenOurSecondSubTypeAllowsTheListType()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->allows(Argument::is($candidateType->reveal()))->willReturn(false);
        $this->ourSubType2->allows(Argument::is($candidateType->reveal()))->willReturn(true);

        static::assertTrue(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        );
    }

    public function testAllowsStaticListTypeReturnsFalseWhenNeitherOfOurSubTypesAllowsTheListType()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->allows(Argument::is($candidateType->reveal()))->willReturn(false);
        $this->ourSubType2->allows(Argument::is($candidateType->reveal()))->willReturn(false);

        static::assertFalse(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        );
    }

    public function testAllowsStaticTypeReturnsTrueWhenOurFirstSubTypeAllowsTheStaticType()
    {
        /** @var ObjectProphecy|StaticType $candidateType */
        $candidateType = $this->prophesize(StaticType::class);

        $this->ourSubType1->allows(Argument::is($candidateType->reveal()))->willReturn(true);
        $this->ourSubType2->allows(Argument::is($candidateType->reveal()))->willReturn(false);

        static::assertTrue($this->type->allowsStaticType($candidateType->reveal()));
    }

    public function testAllowsStaticTypeReturnsTrueWhenOurSecondSubTypeAllowsTheStaticType()
    {
        /** @var ObjectProphecy|StaticType $candidateType */
        $candidateType = $this->prophesize(StaticType::class);

        $this->ourSubType1->allows(Argument::is($candidateType->reveal()))->willReturn(false);
        $this->ourSubType2->allows(Argument::is($candidateType->reveal()))->willReturn(true);

        static::assertTrue($this->type->allowsStaticType($candidateType->reveal()));
    }

    public function testAllowsStaticTypeReturnsFalseWhenNeitherOfOurSubTypesAllowsTheStaticType()
    {
        /** @var ObjectProphecy|StaticType $candidateType */
        $candidateType = $this->prophesize(StaticType::class);

        $this->ourSubType1->allows(Argument::is($candidateType->reveal()))->willReturn(false);
        $this->ourSubType2->allows(Argument::is($candidateType->reveal()))->willReturn(false);

        static::assertFalse($this->type->allowsStaticType($candidateType->reveal()));
    }

    public function testGetSummaryReturnsTheCorrectString()
    {
        $this->ourSubType1->getSummary()->willReturn('sub-type-1');
        $this->ourSubType2->getSummary()->willReturn('sub-type-2');

        static::assertSame('sub-type-1|sub-type-2', $this->type->getSummary());
    }

    public function testMergeWithMultipleTypeReturnsANewMultipleTypeWithBothSetsOfSubTypesCombined()
    {
        /** @var ObjectProphecy|MultipleType $otherType */
        $otherType = $this->prophesize(MultipleType::class);
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        $theirSubType2 = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->getSummary()->willReturn('our-sub-type-1');
        $this->ourSubType2->getSummary()->willReturn('our-sub-type-2');
        $theirSubType1->getSummary()->willReturn('their-sub-type-1');
        $theirSubType2->getSummary()->willReturn('their-sub-type-2');

        $result = $this->type->mergeWithMultipleType($otherType->reveal(), [
            $theirSubType1->reveal(),
            $theirSubType2->reveal()
        ]);

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame(
            'our-sub-type-1|our-sub-type-2|their-sub-type-1|their-sub-type-2',
            $result->getSummary()
        );
    }

    public function testMergeWithStaticListTypeReturnsANewMultipleTypeWithListTypeAddedAsASubType()
    {
        /** @var ObjectProphecy|StaticListType $otherType */
        $otherType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        $this->ourSubType1->getSummary()->willReturn('our-sub-type-1');
        $this->ourSubType2->getSummary()->willReturn('our-sub-type-2');
        $otherType->getSummary()->willReturn('list<their-element-type>');

        $result = $this->type->mergeWithStaticListType($otherType->reveal(), $elementType->reveal());

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame(
            'our-sub-type-1|our-sub-type-2|list<their-element-type>',
            $result->getSummary()
        );
    }

    public function testMergeWithStaticTypeReturnsANewMultipleTypeWithStaticTypeAddedAsASubType()
    {
        /** @var ObjectProphecy|StaticType $otherType */
        $otherType = $this->prophesize(StaticType::class);

        $this->ourSubType1->getSummary()->willReturn('our-sub-type-1');
        $this->ourSubType2->getSummary()->willReturn('our-sub-type-2');
        $otherType->getSummary()->willReturn('their-type');

        $result = $this->type->mergeWithStaticType($otherType->reveal());

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame(
            'our-sub-type-1|our-sub-type-2|their-type',
            $result->getSummary()
        );
    }
}
