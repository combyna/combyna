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
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactory;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NothingExpression;
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
 * Class StaticTypeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticTypeTest extends TestCase
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
     * @var StaticType
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
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);
    }

    public function testCannotBeUsedToMatchAStaticListExpression()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'StaticListExpression must be matched with a StaticListType, not a StaticType'
        );

        $this->createType(StaticListExpression::class);
    }

    public function testCannotBeUsedToMatchANonStaticExpression()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
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

        static::assertTrue(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
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

        static::assertFalse(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsStaticReturnsTrueWhenStaticIsInstanceOfClass()
    {
        $this->createType(NumberExpression::class);
        /** @var ObjectProphecy|NumberExpression $static */
        $static = $this->prophesize(NumberExpression::class);

        static::assertTrue($this->type->allowsStatic($static->reveal()));
    }

    public function testAllowsStaticReturnsFalseWhenStaticIsNotAnInstanceOfClass()
    {
        $this->createType(NumberExpression::class);
        /** @var ObjectProphecy|TextExpression $static */
        $static = $this->prophesize(TextExpression::class);

        static::assertFalse($this->type->allowsStatic($static->reveal()));
    }

    public function testAllowsStaticListTypeAlwaysReturnsFalse()
    {
        $this->createType(TextExpression::class);
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $theirElementType */
        $theirElementType = $this->prophesize(TypeInterface::class);

        static::assertFalse(
            $this->type->allowsStaticListType($candidateType->reveal(), $theirElementType->reveal())
        );
    }

    public function testAllowsStaticTypeReturnsTrueWhenBothMatchTheSameStaticClass()
    {
        $this->createType(TextExpression::class);
        $candidateType = new StaticType(TextExpression::class, $this->validationContext->reveal());

        static::assertTrue($this->type->allowsStaticType($candidateType));
    }

    public function testAllowsStaticTypeReturnsFalseWhenCandidateTypeMatchesADifferentStaticClass()
    {
        $this->createType(TextExpression::class);
        $candidateType = new StaticType(NumberExpression::class, $this->validationContext->reveal());

        static::assertFalse($this->type->allowsStaticType($candidateType));
    }

    public function testCoerceNativeJustReturnsAnExistingCompatibleStatic()
    {
        $this->createType(TextExpression::class);
        $static = $this->prophesize(TextExpression::class);

        static::assertSame(
            $static->reveal(),
            $this->type->coerceNative(
                $static->reveal(),
                $this->staticExpressionFactory,
                $this->bagFactory,
                $this->evaluationContext->reveal()
            )
        );
    }

    public function testCoerceNativeCoercesABooleanToABooleanExpression()
    {
        $this->createType(BooleanExpression::class);
        $static = $this->type->coerceNative(
            true,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(BooleanExpression::class, $static);
        static::assertTrue($static->toNative());
    }

    public function testCoerceNativeThrowsWhenIncompatibleNativeIsGivenForBoolean()
    {
        $this->createType(BooleanExpression::class);

        $this->expectException(IncompatibleNativeForCoercionException::class);
        $this->expectExceptionMessage(
            'Expected boolean, got integer'
        );

        $this->type->coerceNative(
            987, // Boolean expects true or false, but we give a number
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );
    }

    public function testCoerceNativeCoercesNullToANothingExpression()
    {
        $this->createType(NothingExpression::class);
        $static = $this->type->coerceNative(
            null,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(NothingExpression::class, $static);
    }

    public function testCoerceNativeThrowsWhenIncompatibleNativeIsGivenForNothing()
    {
        $this->createType(NothingExpression::class);

        $this->expectException(IncompatibleNativeForCoercionException::class);
        $this->expectExceptionMessage(
            'Expected null, got string'
        );

        $this->type->coerceNative(
            'my non-null value', // Nothing expects null, but we give a string
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );
    }

    public function testCoerceNativeCoercesAnIntegerToANumberExpression()
    {
        $this->createType(NumberExpression::class);
        $static = $this->type->coerceNative(
            21,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(NumberExpression::class, $static);
        static::assertSame(21, $static->toNative());
    }

    public function testCoerceNativeCoercesAFloatToANumberExpression()
    {
        $this->createType(NumberExpression::class);
        $static = $this->type->coerceNative(
            1001.4,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(NumberExpression::class, $static);
        static::assertSame(1001.4, $static->toNative());
    }

    public function testCoerceNativeCoercesANumericStringToANumberExpression()
    {
        $this->createType(NumberExpression::class);
        $static = $this->type->coerceNative(
            '1001.4',
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(NumberExpression::class, $static);
        static::assertSame(1001.4, $static->toNative());
    }

    public function testCoerceNativeThrowsWhenIncompatibleNativeIsGivenForNumber()
    {
        $this->createType(NumberExpression::class);

        $this->expectException(IncompatibleNativeForCoercionException::class);
        $this->expectExceptionMessage(
            'Expected int, float or numeric string, got string'
        );

        $this->type->coerceNative(
            'my non-numeric string', // Text expects a number, but we give a string
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );
    }

    public function testCoerceNativeCoercesAStringToATextExpression()
    {
        $this->createType(TextExpression::class);
        $static = $this->type->coerceNative(
            'hello world!',
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(TextExpression::class, $static);
        static::assertSame('hello world!', $static->toNative());
    }

    public function testCoerceNativeThrowsWhenIncompatibleNativeIsGivenForText()
    {
        $this->createType(TextExpression::class);

        $this->expectException(IncompatibleNativeForCoercionException::class);
        $this->expectExceptionMessage(
            'Expected string, got integer'
        );

        $this->type->coerceNative(
            1001, // Text expects a string, but we give a number
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );
    }

    /**
     * @dataProvider staticClassWithTypeProvider
     */
    public function testGetSummaryReturnsTheMatchedStaticType($staticClass, $expectedType)
    {
        $this->createType($staticClass);

        static::assertSame($expectedType, $this->type->getSummary());
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

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame('number|their-sub-type-1|their-sub-type-2', $result->getSummary());
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

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame('number|list<text>', $result->getSummary());
    }

    public function testMergeWithStaticTypeReturnsThisTypeWhenBothAreEquivalent()
    {
        $this->createType(TextExpression::class);

        $result = $this->type->mergeWithStaticType(
            new StaticType(TextExpression::class, $this->validationContext->reveal())
        );

        static::assertSame($this->type, $result);
    }

    public function testMergeWithStaticTypeReturnsANewMultipleTypeWithBothStatics()
    {
        $this->createType(TextExpression::class);
        $otherType = new StaticType(NumberExpression::class, $this->validationContext->reveal());

        $result = $this->type->mergeWithStaticType($otherType);

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame('text|number', $result->getSummary());
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
        $this->type = new StaticType($staticClass, $this->validationContext->reveal());
    }
}
