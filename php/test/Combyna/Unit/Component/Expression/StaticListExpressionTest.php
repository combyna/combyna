<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression;

use Combyna\Component\Bag\StaticListInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\StaticExpressionFactoryInterface;
use Combyna\Component\Expression\StaticListExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\TypeInterface;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class StaticListExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var StaticListExpression
     */
    private $expression;

    /**
     * @var ObjectProphecy|StaticExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ObjectProphecy|StaticListInterface
     */
    private $staticList;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->expressionFactory = $this->prophesize(StaticExpressionFactoryInterface::class);
        $this->staticList = $this->prophesize(StaticListInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->expressionFactory->createTextExpression(Argument::any())->will($this->noBind(function (array $args) {
            /** @var ObjectProphecy|TextExpression $textExpression */
            $textExpression = $this->prophesize(TextExpression::class);
            $textExpression->toNative()->willReturn($args[0]);

            return $textExpression;
        }));

        $this->expression = new StaticListExpression($this->expressionFactory->reveal(), $this->staticList->reveal());
    }

    public function testConcatenateReturnsATextExpressionWithTheConcatenatedElementTexts()
    {
        $this->staticList->concatenate('')->willReturn('concatenated element contents');

        $result = $this->expression->concatenate();

        $this->assert($result)->isAnInstanceOf(TextExpression::class);
        $this->assert($result->toNative())->exactlyEquals('concatenated element contents');
    }

    public function testElementsMatchReturnsTrueWhenExpected()
    {
        /** @var ObjectProphecy|TypeInterface $candidateElementType */
        $candidateElementType = $this->prophesize(TypeInterface::class);
        $this->staticList->elementsMatch(Argument::is($candidateElementType->reveal()))->willReturn(true);

        $this->assert($this->expression->elementsMatch($candidateElementType->reveal()))->isTrue;
    }

    public function testElementsMatchReturnsFalseWhenExpected()
    {
        /** @var ObjectProphecy|TypeInterface $candidateElementType */
        $candidateElementType = $this->prophesize(TypeInterface::class);
        $this->staticList->elementsMatch(Argument::is($candidateElementType->reveal()))->willReturn(false);

        $this->assert($this->expression->elementsMatch($candidateElementType->reveal()))->isFalse;
    }

    public function testGetTypeReturnsTheStaticListType()
    {
        $this->assert($this->expression->getType())->exactlyEquals('static-list');
    }

    public function testMapReturnsANewStaticListExpressionWithInnerListMapped()
    {
        /** @var ObjectProphecy|StaticListExpression $resultListExpression */
        $resultListExpression = $this->prophesize(StaticListExpression::class);
        /** @var ObjectProphecy|StaticListInterface $resultList */
        $resultList = $this->prophesize(StaticListInterface::class);
        /** @var ObjectProphecy|ExpressionInterface $mapExpression */
        $mapExpression = $this->prophesize(ExpressionInterface::class);
        $this->expressionFactory->createStaticListExpression(Argument::is($resultList->reveal()))
            ->willReturn($resultListExpression);
        $this->staticList->map(
            'my_item',
            'my_item_index',
            Argument::is($mapExpression->reveal()),
            Argument::is($this->evaluationContext->reveal())
        )->willReturn($resultList->reveal());

        $this->assert($this->expression->map(
            'my_item',
            'my_item_index',
            $mapExpression->reveal(),
            $this->evaluationContext->reveal()
        ))->isTheSameAs($resultListExpression->reveal());
    }

    public function testToNativeReturnsTheNativeArrayValue()
    {
        $this->staticList->toArray()->willReturn(['first', 'second']);

        $this->assert($this->expression->toNative())->exactlyEquals(['first', 'second']);
    }

    public function testToStaticReturnsItself()
    {
        $this->assert($this->expression->toStatic($this->evaluationContext->reveal()))
            ->exactlyEquals($this->expression);
    }
}
