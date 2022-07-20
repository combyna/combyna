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
use Combyna\Component\Expression\NothingExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticExpressionFactory;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\StaticListExpression;
use Combyna\Component\Expression\StaticStructureExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\AnyType;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class AnyTypeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AnyTypeTest extends TestCase
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
     * @var AnyType
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

        $this->type = new AnyType($this->validationContext->reveal());
    }

    public function testAllowsMultipleTypeReturnsTrue()
    {
        /** @var ObjectProphecy|MultipleType $candidateType */
        $candidateType = $this->prophesize(MultipleType::class);
        $theirSubType1 = $this->prophesize(TypeInterface::class);
        $theirSubType2 = $this->prophesize(TypeInterface::class);

        static::assertTrue(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        );
    }

    public function testAllowsStaticListTypeReturnsTrue()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        static::assertTrue(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        );
    }

    public function testAllowsStaticTypeReturnsTrue()
    {
        /** @var ObjectProphecy|StaticType $candidateType */
        $candidateType = $this->prophesize(StaticType::class);

        static::assertTrue($this->type->allowsStaticType($candidateType->reveal()));
    }

    public function testCoerceNativeJustReturnsAnExistingStatic()
    {
        $static = $this->prophesize(StaticInterface::class);

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
        $static = $this->type->coerceNative(
            true,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(BooleanExpression::class, $static);
        static::assertTrue($static->toNative());
    }

    public function testCoerceNativeCoercesAnIntegerToANumberExpression()
    {
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
        $static = $this->type->coerceNative(
            1001.4,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(NumberExpression::class, $static);
        static::assertSame(1001.4, $static->toNative());
    }

    public function testCoerceNativeCoercesAStringToATextExpression()
    {
        $static = $this->type->coerceNative(
            'hello world!',
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(TextExpression::class, $static);
        static::assertSame('hello world!', $static->toNative());
    }

    public function testCoerceNativeCoercesNullToANothingExpression()
    {
        $static = $this->type->coerceNative(
            null,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        static::assertInstanceOf(NothingExpression::class, $static);
    }

    public function testCoerceNativeCoercesAnEmptyArrayToAnEmptyListExpression()
    {
        $static = $this->type->coerceNative(
            [],
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        /** @var StaticListExpression $static */
        static::assertInstanceOf(StaticListExpression::class, $static);
        static::assertEmpty($static->getElementStatics());
    }

    public function testCoerceNativeCoercesAnIndexedArrayWithTwoNumbersToAListExpressionWithTwoNumbers()
    {
        $static = $this->type->coerceNative(
            [21, 1004],
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        /** @var StaticListExpression $static */
        static::assertInstanceOf(StaticListExpression::class, $static);
        $statics = $static->getElementStatics();
        static::assertInstanceOf(NumberExpression::class, $statics[0]);
        static::assertSame(21, $statics[0]->toNative());
        static::assertInstanceOf(NumberExpression::class, $statics[1]);
        static::assertSame(1004, $statics[1]->toNative());
    }

    public function testCoerceNativeCoercesAnAssociativeArrayWithAStringAndNumberToAStructureExpression()
    {
        $static = $this->type->coerceNative(
            [
                'my_string' => 'hello!',
                'my_number' => 42
            ],
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        /** @var StaticStructureExpression $static */
        static::assertInstanceOf(StaticStructureExpression::class, $static);
        $staticBag = $static->getAttributeStaticBag();
        static::assertInstanceOf(TextExpression::class, $staticBag->getStatic('my_string'));
        static::assertSame('hello!', $staticBag->getStatic('my_string')->toNative());
        static::assertInstanceOf(NumberExpression::class, $staticBag->getStatic('my_number'));
        static::assertSame(42, $staticBag->getStatic('my_number')->toNative());
    }

    public function testGetSummaryReturnsTheCorrectString()
    {
        static::assertSame('*', $this->type->getSummary());
    }

    public function testMergeWithMultipleTypeReturnsANewMultipleTypeWithBothSetsOfSubTypesCombined()
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

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame(
            '*|their-sub-type-1|their-sub-type-2'
        , $result->getSummary());
    }

    public function testMergeWithStaticListTypeReturnsANewMultipleTypeWithListTypeAddedAsASubType()
    {
        /** @var ObjectProphecy|StaticListType $otherType */
        $otherType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);
        $otherType->getSummary()->willReturn('list<their-element-type>');

        $result = $this->type->mergeWithStaticListType($otherType->reveal(), $elementType->reveal());

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame(
            '*|list<their-element-type>'
        , $result->getSummary());
    }

    public function testMergeWithStaticTypeReturnsANewMultipleTypeWithStaticTypeAddedAsASubType()
    {
        /** @var ObjectProphecy|StaticType $otherType */
        $otherType = $this->prophesize(StaticType::class);
        $otherType->getSummary()->willReturn('their-type');

        $result = $this->type->mergeWithStaticType($otherType->reveal());

        static::assertInstanceOf(MultipleType::class, $result);
        static::assertSame(
            '*|their-type'
        , $result->getSummary());
    }
}
