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

use ArrayIterator;
use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\Expression\Evaluation\BagEvaluationContextFactory;
use Combyna\Component\Bag\FixedStaticBagModel;
use Combyna\Component\Bag\FixedStaticDefinition;
use Combyna\Component\Expression\Evaluation\EvaluationContextFactory;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\StaticExpressionFactory;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\Exception\IncompatibleNativeForCoercionException;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticStructureType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class StaticStructureTypeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticStructureTypeTest extends TestCase
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
     * @var StaticStructureType
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

        $this->type = new StaticStructureType(
            new FixedStaticBagModel(
                $this->bagFactory,
                $this->staticExpressionFactory,
                $this->bagEvaluationContextFactory,
                [
                    new FixedStaticDefinition(
                        'human',
                        new StaticStructureType(
                            new FixedStaticBagModel(
                                $this->bagFactory,
                                $this->staticExpressionFactory,
                                $this->bagEvaluationContextFactory,
                                [
                                    new FixedStaticDefinition(
                                        'first-name',
                                        new StaticType(TextExpression::class, $this->validationContext->reveal()),
                                        new TextExpression('(default for first-name)')
                                    ),
                                    new FixedStaticDefinition(
                                        'second-name',
                                        new StaticType(TextExpression::class, $this->validationContext->reveal()),
                                        new TextExpression('(default for second-name)')
                                    )
                                ]
                            ),
                            $this->validationContext->reveal()
                        )
                    ),
                    new FixedStaticDefinition(
                        'dog',
                        new StaticStructureType(
                            new FixedStaticBagModel(
                                $this->bagFactory,
                                $this->staticExpressionFactory,
                                $this->bagEvaluationContextFactory,
                                [
                                    new FixedStaticDefinition(
                                        'name',
                                        new StaticType(TextExpression::class, $this->validationContext->reveal()),
                                        new TextExpression('(default for name)')
                                    ),
                                    new FixedStaticDefinition(
                                        'food',
                                        new StaticType(TextExpression::class, $this->validationContext->reveal()),
                                        new TextExpression('(default for food)')
                                    )
                                ]
                            ),
                            $this->validationContext->reveal()
                        )
                    )
                ]
            ),
            $this->validationContext->reveal()
        );
    }

    public function testCoerceNativeThrowsWhenNonArrayOrObjectIsGiven()
    {
        $this->setExpectedException(
            IncompatibleNativeForCoercionException::class,
            'Static structure type expects an array or stdClass instance, integer given'
        );

        $this->type->coerceNative(
            21,
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );
    }

    public function testCoerceNativeThrowsWhenNonStdclassObjectIsGiven()
    {
        $this->setExpectedException(
            IncompatibleNativeForCoercionException::class,
            'Static structure type expects an array or stdClass instance, instance of ArrayIterator given'
        );

        $this->type->coerceNative(
            new ArrayIterator(),
            $this->staticExpressionFactory,
            $this->bagFactory,
            $this->evaluationContext->reveal()
        );
    }

    // NB: See StaticStructureTypeIntegratedTest for further tests of StaticStructureType/coercion

    public function testGetSummaryReturnsTheCorrectString()
    {
        $this->assert($this->type->getSummary())->exactlyEquals(
            'structure<{human: structure<{first-name: text, second-name: text}>, dog: structure<{name: text, food: text}>}>'
        );
    }

    public function testMergeWithMultipleTypeReturnsANewMultipleTypeWithStructureTypeAsASubType()
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
            'structure<{human: structure<{first-name: text, second-name: text}>, dog: structure<{name: text, food: text}>}>|their-sub-type-1|their-sub-type-2'
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
            'structure<{human: structure<{first-name: text, second-name: text}>, dog: structure<{name: text, food: text}>}>|list<their-element-type>'
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
            'structure<{human: structure<{first-name: text, second-name: text}>, dog: structure<{name: text, food: text}>}>|their-type'
        );
    }
}
