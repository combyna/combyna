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
use Combyna\Component\Validator\Context\ValidationContextInterface;
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

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->type = new VoidType('my context', $this->validationContext->reveal());
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

        static::assertTrue(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
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

        static::assertFalse(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsStaticAlwaysReturnsFalse()
    {
        /** @var ObjectProphecy|StaticInterface $static */
        $static = $this->prophesize(StaticInterface::class);

        static::assertFalse($this->type->allowsStatic($static->reveal()));
    }

    public function testAllowsStaticListTypeAlwaysReturnsFalse()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $theirElementType */
        $theirElementType = $this->prophesize(TypeInterface::class);

        static::assertFalse(
            $this->type->allowsStaticListType($candidateType->reveal(), $theirElementType->reveal())
        );
    }

    public function testAllowsStaticTypeAlwaysReturnsFalse()
    {
        $candidateType = new StaticType(StaticInterface::class, $this->validationContext->reveal());

        static::assertFalse($this->type->allowsStaticType($candidateType));
    }

    public function testGetSummaryReturnsTheCorrectString()
    {
        static::assertSame('void<my context>', $this->type->getSummary());
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

        static::assertSame($otherType->reveal(), $result);
    }

    public function testMergeWithStaticListTypeJustReturnsTheStaticList()
    {
        /** @var ObjectProphecy|StaticListType $otherType */
        $otherType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        $result = $this->type->mergeWithStaticListType($otherType->reveal(), $elementType->reveal());

        static::assertSame($otherType->reveal(), $result);
    }

    public function testMergeWithStaticTypeJustReturnsTheStaticType()
    {
        $staticType = new StaticType(StaticInterface::class, $this->validationContext->reveal());

        $result = $this->type->mergeWithStaticType($staticType);

        static::assertSame($staticType, $result);
    }
}
