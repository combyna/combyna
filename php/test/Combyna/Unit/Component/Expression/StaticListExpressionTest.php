<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
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
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
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

        static::assertInstanceOf(TextExpression::class, $result);
        static::assertSame('concatenated element contents', $result->toNative());
    }

    public function testElementsMatchReturnsTrueWhenExpected()
    {
        /** @var ObjectProphecy|TypeInterface $candidateElementType */
        $candidateElementType = $this->prophesize(TypeInterface::class);
        $this->staticList->elementsMatch(Argument::is($candidateElementType->reveal()))->willReturn(true);

        static::assertTrue($this->expression->elementsMatch($candidateElementType->reveal()));
    }

    public function testElementsMatchReturnsFalseWhenExpected()
    {
        /** @var ObjectProphecy|TypeInterface $candidateElementType */
        $candidateElementType = $this->prophesize(TypeInterface::class);
        $this->staticList->elementsMatch(Argument::is($candidateElementType->reveal()))->willReturn(false);

        static::assertFalse($this->expression->elementsMatch($candidateElementType->reveal()));
    }

    public function testGetTypeReturnsTheStaticListType()
    {
        static::assertSame('static-list', $this->expression->getType());
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

        static::assertSame(
            $resultListExpression->reveal(),
            $this->expression->map(
                'my_item',
                'my_item_index',
                $mapExpression->reveal(),
                $this->evaluationContext->reveal()
            )
        );
    }

    public function testToNativeReturnsTheNativeArrayValue()
    {
        $this->staticList->toArray()->willReturn(['first', 'second']);

        static::assertSame(['first', 'second'], $this->expression->toNative());
    }

    public function testToStaticReturnsItself()
    {
        static::assertSame($this->expression, $this->expression->toStatic($this->evaluationContext->reveal()));
    }

    public function testWithElementsReturnsTheSameListExpressionObjectWhenAllAndOnlySpecifiedStaticsAreAlreadyPresent()
    {
        $firstElement = new TextExpression('first element');
        $secondElement = new TextExpression('second element');
        $staticListExpression = new StaticListExpression(
            $this->expressionFactory->reveal(),
            $this->staticList->reveal()
        );
        $this->staticList->withElements([$firstElement, $secondElement])
            ->willReturn($this->staticList);

        static::assertSame($staticListExpression, $staticListExpression->withElements([$firstElement, $secondElement]));
    }

    public function testWithElementsReturnsANewListWhenAtLeastOneStaticIsNotAlreadyPresent()
    {
        $firstElement = new TextExpression('first element');
        $secondElement = new TextExpression('second element');
        $staticListExpression = new StaticListExpression(
            $this->expressionFactory->reveal(),
            $this->staticList->reveal()
        );
        $newElement = new TextExpression('new element');
        $newStaticList = $this->prophesize(StaticListInterface::class);
        $newStaticList->getElementStatics()
            ->willReturn([$firstElement, $secondElement, $newElement]);
        $this->staticList->withElements([$firstElement, $secondElement, $newElement])
            ->willReturn($newStaticList);

        $newStaticListExpression = $staticListExpression->withElements([$firstElement, $secondElement, $newElement]);

        static::assertNotSame($staticListExpression, $newStaticListExpression);
        static::assertSame($newElement, $newStaticListExpression->getElementStatics()[2]);
    }
}
