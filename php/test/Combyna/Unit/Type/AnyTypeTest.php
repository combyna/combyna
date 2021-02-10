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

        $this->assert(
            $this->type->allowsMultipleType($candidateType->reveal(), [
                $theirSubType1->reveal(),
                $theirSubType2->reveal()
            ])
        )->isTrue;
    }

    public function testAllowsStaticListTypeReturnsTrue()
    {
        /** @var ObjectProphecy|StaticListType $candidateType */
        $candidateType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);

        $this->assert(
            $this->type->allowsStaticListType($candidateType->reveal(), $elementType->reveal())
        )->isTrue;
    }

    public function testAllowsStaticTypeReturnsTrue()
    {
        /** @var ObjectProphecy|StaticType $candidateType */
        $candidateType = $this->prophesize(StaticType::class);

        $this->assert($this->type->allowsStaticType($candidateType->reveal()))->isTrue;
    }

    public function testCoerceNativeJustReturnsAnExistingStatic()
    {
        $static = $this->prophesize(StaticInterface::class);

        $this->assert(
            $this->type->coerceNative(
                $static->reveal(),
                $this->staticExpressionFactory,
                $this->bagFactory,
                $this->evaluationContext->reveal()
            )
        )->exactlyEquals($static->reveal());
    }

    public function testCoerceNativeCoercesABooleanToABooleanExpression()
    {
        $static = $this->type->coerceNative(
            true,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        $this->assert($static)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($static->toNative())->isTrue;
    }

    public function testCoerceNativeCoercesAnIntegerToANumberExpression()
    {
        $static = $this->type->coerceNative(
            21,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        $this->assert($static)->isAnInstanceOf(NumberExpression::class);
        $this->assert($static->toNative())->exactlyEquals(21);
    }

    public function testCoerceNativeCoercesAFloatToANumberExpression()
    {
        $static = $this->type->coerceNative(
            1001.4,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        $this->assert($static)->isAnInstanceOf(NumberExpression::class);
        $this->assert($static->toNative())->exactlyEquals(1001.4);
    }

    public function testCoerceNativeCoercesAStringToATextExpression()
    {
        $static = $this->type->coerceNative(
            'hello world!',
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        $this->assert($static)->isAnInstanceOf(TextExpression::class);
        $this->assert($static->toNative())->exactlyEquals('hello world!');
    }

    public function testCoerceNativeCoercesNullToANothingExpression()
    {
        $static = $this->type->coerceNative(
            null,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );

        $this->assert($static)->isAnInstanceOf(NothingExpression::class);
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
        $this->assert($static)->isAnInstanceOf(StaticListExpression::class);
        $this->assert(empty($static->getElementStatics()))->isTrue;
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
        $this->assert($static)->isAnInstanceOf(StaticListExpression::class);
        $statics = $static->getElementStatics();
        $this->assert($statics[0])->isAnInstanceOf(NumberExpression::class);
        $this->assert($statics[0]->toNative())->exactlyEquals(21);
        $this->assert($statics[1])->isAnInstanceOf(NumberExpression::class);
        $this->assert($statics[1]->toNative())->exactlyEquals(1004);
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
        $this->assert($static)->isAnInstanceOf(StaticStructureExpression::class);
        $staticBag = $static->getAttributeStaticBag();
        $this->assert($staticBag->getStatic('my_string'))->isAnInstanceOf(TextExpression::class);
        $this->assert($staticBag->getStatic('my_string')->toNative())->exactlyEquals('hello!');
        $this->assert($staticBag->getStatic('my_number'))->isAnInstanceOf(NumberExpression::class);
        $this->assert($staticBag->getStatic('my_number')->toNative())->exactlyEquals(42);
    }

    public function testGetSummaryReturnsTheCorrectString()
    {
        $this->assert($this->type->getSummary())->exactlyEquals('*');
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

        $this->assert($result)->isAnInstanceOf(MultipleType::class);
        $this->assert($result->getSummary())->exactlyEquals(
            '*|their-sub-type-1|their-sub-type-2'
        );
    }

    public function testMergeWithStaticListTypeReturnsANewMultipleTypeWithListTypeAddedAsASubType()
    {
        /** @var ObjectProphecy|StaticListType $otherType */
        $otherType = $this->prophesize(StaticListType::class);
        /** @var ObjectProphecy|TypeInterface $elementType */
        $elementType = $this->prophesize(TypeInterface::class);
        $otherType->getSummary()->willReturn('list<their-element-type>');

        $result = $this->type->mergeWithStaticListType($otherType->reveal(), $elementType->reveal());

        $this->assert($result)->isAnInstanceOf(MultipleType::class);
        $this->assert($result->getSummary())->exactlyEquals(
            '*|list<their-element-type>'
        );
    }

    public function testMergeWithStaticTypeReturnsANewMultipleTypeWithStaticTypeAddedAsASubType()
    {
        /** @var ObjectProphecy|StaticType $otherType */
        $otherType = $this->prophesize(StaticType::class);
        $otherType->getSummary()->willReturn('their-type');

        $result = $this->type->mergeWithStaticType($otherType->reveal());

        $this->assert($result)->isAnInstanceOf(MultipleType::class);
        $this->assert($result->getSummary())->exactlyEquals(
            '*|their-type'
        );
    }
}
