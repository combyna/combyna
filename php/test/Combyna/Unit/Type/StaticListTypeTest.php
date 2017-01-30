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

use Combyna\Expression\NumberExpression;
use Combyna\Expression\StaticListExpression;
use Combyna\Type\MultipleType;
use Combyna\Type\StaticListType;
use Combyna\Type\StaticType;
use Combyna\Type\TypeInterface;
use Concise\Core\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class StaticListTypeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListTypeTest extends TestCase
{
    /**
     * @var ObjectProphecy|TypeInterface
     */
    private $elementType;

    /**
     * @var StaticListType
     */
    private $type;

    public function setUp()
    {
        $this->elementType = $this->prophesize(TypeInterface::class);

        $this->type = new StaticListType($this->elementType->reveal());
    }

    public function testAllowsMultipleTypeReturnsTrueIfAllItsSubTypesAreAllowedByUs()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType1 */
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        /** @var ObjectProphecy|TypeInterface $theirSubType2 */
        $theirSubType2 = $this->prophesize(TypeInterface::class);
        $theirSubType1->isAllowedByStaticListType(Argument::is($this->type))->willReturn(true);
        $theirSubType2->isAllowedByStaticListType(Argument::is($this->type))->willReturn(true);

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
        $theirSubType1->isAllowedByStaticListType(Argument::is($this->type))->willReturn(true);
        $theirSubType2->isAllowedByStaticListType(Argument::is($this->type))->willReturn(false);

        $this->assert(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        )->isFalse;
    }

    public function testAllowsStaticReturnsFalseForANonStaticListExpressionStatic()
    {
        /** @var ObjectProphecy|NumberExpression $candidateType */
        $candidateType = $this->prophesize(NumberExpression::class);

        $this->assert($this->type->allowsStatic($candidateType->reveal()))->isFalse;
    }
    
    public function testAllowsStaticReturnsTrueWhenStaticListExpressionGivenAndElementTypesMatch()
    {
        /** @var ObjectProphecy|StaticListExpression $candidateType */
        $candidateType = $this->prophesize(StaticListExpression::class);
        $candidateType->elementsMatch(Argument::is($this->elementType->reveal()))->willReturn(true);

        $this->assert($this->type->allowsStatic($candidateType->reveal()))->isTrue;
    }

    public function testAllowsStaticReturnsFalseWhenStaticListExpressionGivenButElementTypesDoNotMatch()
    {
        /** @var ObjectProphecy|StaticListExpression $static */
        $static = $this->prophesize(StaticListExpression::class);
        $static->elementsMatch(Argument::is($this->elementType->reveal()))->willReturn(false);

        $this->assert($this->type->allowsStatic($static->reveal()))->isFalse;
    }

    public function testAllowsStaticListTypeReturnsTrueWhenOurElementTypeAllowsTheirElementType()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);
        $this->elementType->allows(Argument::is($elementType->reveal()))->willReturn(true);

        $this->assert(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        )->isTrue;
    }

    public function testAllowsStaticListTypeReturnsFalseWhenOurElementTypeDoesNotAllowTheirElementType()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);
        $this->elementType->allows(Argument::is($elementType->reveal()))->willReturn(false);

        $this->assert(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        )->isFalse;
    }

    public function testAllowsStaticTypeAlwaysReturnsFalse()
    {
        /** @var ObjectProphecy|StaticType $candidateType */
        $candidateType = $this->prophesize(StaticType::class);

        $this->assert($this->type->allowsStaticType($candidateType->reveal()))->isFalse;
    }
    
    public function testGetSummaryReturnsTheCorrectRepresentation()
    {
        $this->elementType->getSummary()->willReturn('our-element-type');

        $this->assert($this->type->getSummary())->exactlyEquals('list<our-element-type>');
    }

    public function testMergeWithMultipleTypeReturnsANewMultipleTypeWithThisStaticAdded()
    {
        $this->elementType->getSummary()->willReturn('our-element-type');
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
        $this->assert($result->getSummary())->exactlyEquals(
            'list<our-element-type>|their-sub-type-1|their-sub-type-2'
        );
    }

    public function testMergeWithStaticListTypeReturnsANewStaticListTypeWithElementTypesCombined()
    {
        /** @var ObjectProphecy|StaticListType $otherType */
        $otherType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $theirElementType */
        $theirElementType = $this->prophesize(TypeInterface::class);
        /** @var ObjectProphecy|MultipleType $combinedElementType */
        $combinedElementType = $this->prophesize(MultipleType::class);
        $combinedElementType->getSummary()->willReturn('our-element-type|their-element-type');
        $this->elementType->mergeWith(Argument::is($theirElementType->reveal()))
            ->willReturn($combinedElementType->reveal());

        $result = $this->type->mergeWithStaticListType($otherType->reveal(), $theirElementType->reveal());

        $this->assert($result)->isAnInstanceOf(StaticListType::class);
        $this->assert($result->getSummary())->exactlyEquals(
            'list<our-element-type|their-element-type>'
        );
    }

    public function testMergeWithStaticTypeReturnsAMultipleTypeWithStaticListAndStatic()
    {
        $this->elementType->getSummary()->willReturn('our-element-type');
        /** @var ObjectProphecy|StaticType $otherType */
        $otherType = $this->prophesize(StaticType::class);
        $otherType->getSummary()->willReturn('their-static-type');

        $result = $this->type->mergeWithStaticType($otherType->reveal());

        $this->assert($result)->isAnInstanceOf(MultipleType::class);
        $this->assert($result->getSummary())->exactlyEquals('list<our-element-type>|their-static-type');
    }
}
