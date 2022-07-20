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

use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\Expression\Evaluation\BagEvaluationContextFactory;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactory;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticExpressionFactory;
use Combyna\Component\Expression\StaticListExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\Exception\IncompatibleNativeForCoercionException;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
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
     * @var BagEvaluationContextFactory
     */
    private $bagEvaluationContextFactory;

    /**
     * @var BagFactory
     */
    private $bagFactory;

    /**
     * @var ObjectProphecy|TypeInterface
     */
    private $elementType;

    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var EvaluationContextFactory
     */
    private $evaluationContextFactory;

    /**
     * @var StaticExpressionFactory
     */
    private $staticExpressionFactory;

    /**
     * @var StaticListType
     */
    private $type;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->evaluationContextFactory = new EvaluationContextFactory();
        $this->staticExpressionFactory = new StaticExpressionFactory();
        $this->bagEvaluationContextFactory = new BagEvaluationContextFactory(
            $this->evaluationContextFactory,
            $this->staticExpressionFactory
        );
        $this->bagFactory = new BagFactory(
            $this->staticExpressionFactory,
            $this->bagEvaluationContextFactory
        );
        $this->elementType = $this->prophesize(TypeInterface::class);
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->type = new StaticListType(
            $this->elementType->reveal(),
            $this->validationContext->reveal()
        );
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
        $theirSubType1->isAllowedByStaticListType(Argument::is($this->type))->willReturn(true);
        $theirSubType2->isAllowedByStaticListType(Argument::is($this->type))->willReturn(false);

        static::assertFalse(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsStaticReturnsFalseForANonStaticListExpressionStatic()
    {
        /** @var ObjectProphecy|NumberExpression $candidateType */
        $candidateType = $this->prophesize(NumberExpression::class);

        static::assertFalse($this->type->allowsStatic($candidateType->reveal()));
    }

    public function testAllowsStaticReturnsTrueWhenStaticListExpressionGivenAndElementTypesMatch()
    {
        /** @var ObjectProphecy|StaticListExpression $candidateType */
        $candidateType = $this->prophesize(StaticListExpression::class);
        $candidateType->elementsMatch(Argument::is($this->elementType->reveal()))->willReturn(true);

        static::assertTrue($this->type->allowsStatic($candidateType->reveal()));
    }

    public function testAllowsStaticReturnsFalseWhenStaticListExpressionGivenButElementTypesDoNotMatch()
    {
        /** @var ObjectProphecy|StaticListExpression $static */
        $static = $this->prophesize(StaticListExpression::class);
        $static->elementsMatch(Argument::is($this->elementType->reveal()))->willReturn(false);

        static::assertFalse($this->type->allowsStatic($static->reveal()));
    }

    public function testAllowsStaticListTypeReturnsTrueWhenOurElementTypeAllowsTheirElementType()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);
        $this->elementType->allows(Argument::is($elementType->reveal()))->willReturn(true);

        static::assertTrue(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        );
    }

    public function testAllowsStaticListTypeReturnsFalseWhenOurElementTypeDoesNotAllowTheirElementType()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);
        $this->elementType->allows(Argument::is($elementType->reveal()))->willReturn(false);

        static::assertFalse(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        );
    }

    public function testAllowsStaticTypeAlwaysReturnsFalse()
    {
        /** @var ObjectProphecy|StaticType $candidateType */
        $candidateType = $this->prophesize(StaticType::class);

        static::assertFalse($this->type->allowsStaticType($candidateType->reveal()));
    }

    public function testCoerceNativeCoercesAndReturnsAnExistingAllowedListStatic()
    {
        $element1 = new TextExpression('first element');
        $element2 = new TextExpression('second element');
        $staticListExpression = new StaticListExpression(
            $this->staticExpressionFactory,
            $this->bagFactory->createStaticList([
                $element1,
                $element2
            ])
        );
        $this->elementType->allowsStatic($element1)
            ->willReturn(true);
        $this->elementType->coerceStatic($element1, $this->evaluationContext)
            ->willReturn($element1);
        $this->elementType->allowsStatic($element2)
            ->willReturn(true);
        $this->elementType->coerceStatic($element2, $this->evaluationContext)
            ->willReturn($element2);

        static::assertSame(
            $staticListExpression,
            $this->type->coerceNative(
                $staticListExpression,
                $this->staticExpressionFactory,
                $this->bagFactory,
                $this->evaluationContext->reveal()
            )
        );
    }

    public function testCoerceNativeThrowsForAnIncompatibleExistingStatic()
    {
        $element = new TextExpression('my element');
        $staticListExpression = new StaticListExpression(
            $this->staticExpressionFactory,
            $this->bagFactory->createStaticList([$element])
        );
        $this->elementType->allowsStatic($element)
            ->willReturn(false);
        $this->elementType->coerceStatic($element, $this->evaluationContext)
            ->willReturn($element);
        $this->elementType->getSummary()
            ->willReturn('my type summary');

        $this->expectException(IncompatibleNativeForCoercionException::class);
        $this->expectExceptionMessage(
            'Static of type "static-list" was given, expected a native or matching static of type "list<my type summary>"'
        );

        $this->type->coerceNative(
            $staticListExpression,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );
    }

    public function testCoerceNativeCoercesAnIndexedArrayWithTwoNumbersToAListExpressionWithTwoNumbers()
    {
        $coercedElement1 = new NumberExpression(21);
        $this->elementType->coerceNative(
            21,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext
        )
            ->willReturn($coercedElement1);
        $coercedElement2 = new NumberExpression(1004);
        $this->elementType->coerceNative(
            1004,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext
        )
            ->willReturn($coercedElement2);

        $static = $this->type->coerceNative(
            [21, 1004],
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        /** @var StaticListExpression $static */
        static::assertInstanceOf(StaticListExpression::class, $static);
        $statics = $static->getElementStatics();
        static::assertSame($coercedElement1, $statics[0]);
        static::assertSame($coercedElement2, $statics[1]);
    }

    public function testCoerceStaticThrowsWhenNonStaticListExpressionIsGiven()
    {
        $nonListStatic = $this->prophesize(TextExpression::class);
        $evaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Expected a %s, got %s',
                StaticListExpression::class,
                get_class($nonListStatic->reveal())
            )
        );

        $this->type->coerceStatic($nonListStatic->reveal(), $evaluationContext->reveal());
    }

    // NB: See StaticListTypeIntegratedTest for further tests of StaticListType/coercion

    public function testGetSummaryReturnsTheCorrectRepresentation()
    {
        $this->elementType->getSummary()->willReturn('our-element-type');

        static::assertSame('list<our-element-type>', $this->type->getSummary());
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

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame(
            'list<our-element-type>|their-sub-type-1|their-sub-type-2',
            $result->getSummary()
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

        static::assertInstanceOf(StaticListType::class, $result);
        static::assertSame(
            'list<our-element-type|their-element-type>',
            $result->getSummary()
        );
    }

    public function testMergeWithStaticTypeReturnsAMultipleTypeWithStaticListAndStatic()
    {
        $this->elementType->getSummary()->willReturn('our-element-type');
        /** @var ObjectProphecy|StaticType $otherType */
        $otherType = $this->prophesize(StaticType::class);
        $otherType->getSummary()->willReturn('their-static-type');

        $result = $this->type->mergeWithStaticType($otherType->reveal());

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame('list<our-element-type>|their-static-type', $result->getSummary());
    }
}
