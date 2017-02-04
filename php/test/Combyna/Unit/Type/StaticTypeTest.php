<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Type;

use Combyna\Expression\ExpressionInterface;
use Combyna\Expression\NumberExpression;
use Combyna\Expression\StaticListExpression;
use Combyna\Expression\TextExpression;
use Combyna\Harness\TestCase;
use Combyna\Type\MultipleType;
use Combyna\Type\StaticListType;
use Combyna\Type\StaticType;
use Combyna\Type\TypeInterface;
use InvalidArgumentException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class StaticTypeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticTypeTest extends TestCase
{
    /**
     * @var StaticType
     */
    private $type;

    public function testCannotBeUsedToMatchAStaticListExpression()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'StaticListExpression must be matched with a StaticListType, not a StaticType'
        );

        $this->createType(StaticListExpression::class);
    }

    public function testCannotBeUsedToMatchANonStaticExpression()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'StaticType must be passed a static expression class'
        );

        $this->createType(ExpressionInterface::class);
    }

    public function testAllowsMultipleTypeReturnsTrueIfAllItsSubTypesAreAllowedByUs()
    {
        $this->createType(NumberExpression::class);
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType1 */
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType2 */
        $theirSubType2 = $this->prophesize(TypeInterface::class);
        $theirSubType1->isAllowedByStaticType(Argument::is($this->type))->willReturn(true);
        $theirSubType2->isAllowedByStaticType(Argument::is($this->type))->willReturn(true);

        $this->assert(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        )->isTrue;
    }

    public function testAllowsMultipleTypeReturnsFalseIfOneOfItsSubTypesIsNotAllowedByUs()
    {
        $this->createType(NumberExpression::class);
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType1 */
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType2 */
        $theirSubType2 = $this->prophesize(TypeInterface::class);
        $theirSubType1->isAllowedByStaticType(Argument::is($this->type))->willReturn(true);
        $theirSubType2->isAllowedByStaticType(Argument::is($this->type))->willReturn(false);

        $this->assert(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        )->isFalse;
    }

    public function testAllowsStaticReturnsTrueWhenStaticIsInstanceOfClass()
    {
        $this->createType(NumberExpression::class);
        /** @var ObjectProphecy|NumberExpression $static */
        $static = $this->prophesize(NumberExpression::class);

        $this->assert($this->type->allowsStatic($static->reveal()))->isTrue;
    }

    public function testAllowsStaticReturnsFalseWhenStaticIsNotAnInstanceOfClass()
    {
        $this->createType(NumberExpression::class);
        /** @var ObjectProphecy|TextExpression $static */
        $static = $this->prophesize(TextExpression::class);

        $this->assert($this->type->allowsStatic($static->reveal()))->isFalse;
    }

    public function testAllowsStaticListTypeAlwaysReturnsFalse()
    {
        $this->createType(TextExpression::class);
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $theirElementType */
        $theirElementType = $this->prophesize(TypeInterface::class);

        $this->assert(
            $this->type->allowsStaticListType($candidateType->reveal(), $theirElementType->reveal())
        )->isFalse;
    }

    public function testAllowsStaticTypeReturnsTrueWhenBothMatchTheSameStaticClass()
    {
        $this->createType(TextExpression::class);
        $candidateType = new StaticType(TextExpression::class);

        $this->assert($this->type->allowsStaticType($candidateType))->isTrue;
    }

    public function testAllowsStaticTypeReturnsFalseWhenCandidateTypeMatchesADifferentStaticClass()
    {
        $this->createType(TextExpression::class);
        $candidateType = new StaticType(NumberExpression::class);

        $this->assert($this->type->allowsStaticType($candidateType))->isFalse;
    }

    /**
     * @dataProvider staticClassWithTypeProvider
     */
    public function testGetSummaryReturnsTheMatchedStaticType($staticClass, $expectedType)
    {
        $this->createType($staticClass);

        $this->assert($this->type->getSummary())->exactlyEquals($expectedType);
    }

    public function testMergeWithMultipleTypeReturnsANewMultipleTypeWithThisStaticAdded()
    {
        $this->createType(NumberExpression::class);
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

        $this->assert($result)->isAnInstanceOf(MultipleType::class);
        $this->assert($result->getSummary())->exactlyEquals('number|their-sub-type-1|their-sub-type-2');
    }

    public function testMergeWithStaticListTypeReturnsAMultipleTypeWithStaticAndStaticList()
    {
        $this->createType(NumberExpression::class);
        /** @var ObjectProphecy|StaticListType $otherType */
        $otherType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);
        $otherType->getSummary()->willReturn('list<text>');

        $result = $this->type->mergeWithStaticListType($otherType->reveal(), $elementType->reveal());

        $this->assert($result)->isAnInstanceOf(MultipleType::class);
        $this->assert($result->getSummary())->exactlyEquals('number|list<text>');
    }

    public function testMergeWithStaticTypeReturnsThisTypeWhenBothAreEquivalent()
    {
        $this->createType(TextExpression::class);

        $result = $this->type->mergeWithStaticType(new StaticType(TextExpression::class));

        $this->assert($result)->exactlyEquals($this->type);
    }

    public function testMergeWithStaticTypeReturnsANewMultipleTypeWithBothStatics()
    {
        $this->createType(TextExpression::class);
        $otherType = new StaticType(NumberExpression::class);

        $result = $this->type->mergeWithStaticType($otherType);

        $this->assert($result)->isAnInstanceOf(MultipleType::class);
        $this->assert($result->getSummary())->exactlyEquals('text|number');
    }

    /**
     * @return array
     */
    public function staticClassWithTypeProvider()
    {
        return [
            'number' => [NumberExpression::class, 'number'],
            'text' => [TextExpression::class, 'text']
        ];
    }

    /**
     * @param string $staticClass
     */
    private function createType($staticClass)
    {
        $this->type = new StaticType($staticClass);
    }
}
