<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Bag;

use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Bag\StaticList;
use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\Evaluation\ExpressionEvaluationContext;
use Combyna\Component\Expression\Evaluation\ScopeEvaluationContext;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticExpressionFactory;
use Combyna\Component\Expression\StaticInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\MultipleType;
use Combyna\Component\Type\StaticType;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use OutOfBoundsException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class StaticListTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class StaticListTest extends TestCase
{
    /**
     * @var BagFactory
     */
    private $bagFactory;

    /**
     * @var StaticExpressionFactory
     */
    private $staticExpressionFactory;

    /**
     * @var StaticList
     */
    private $staticList;

    public function setUp()
    {
        $this->staticExpressionFactory = new StaticExpressionFactory();
        $this->bagFactory = new BagFactory($this->staticExpressionFactory);

        $this->staticList = new StaticList(
            $this->bagFactory,
            $this->staticExpressionFactory,
            [
                new TextExpression('first element'),
                new NumberExpression(2),
                new TextExpression('third element')
            ]
        );
    }

    public function testConcatenateJoinsTheElementsTogetherCastedToNativeStrings()
    {
        self::assertSame('first element,2,third element', $this->staticList->concatenate(','));
    }

    public function testListIsCountable()
    {
        self::assertSame(3, count($this->staticList));
    }

    public function testElementsMatchReturnsTrueForATypeThatMatchesAllTheElements()
    {
        $type = new MultipleType([
            new StaticType(TextExpression::class),
            new StaticType(NumberExpression::class)
        ]);

        self::assertTrue($this->staticList->elementsMatch($type));
    }

    public function testElementsMatchReturnsFalseForATypeThatMatchesNoneOfTheElements()
    {
        $type = new StaticType(BooleanExpression::class);

        self::assertFalse($this->staticList->elementsMatch($type));
    }

    public function testElementsMatchReturnsFalseForATypeThatMatchesOnlySomeOfTheElements()
    {
        $type = new MultipleType([
            new StaticType(TextExpression::class),
            new StaticType(BooleanExpression::class)
        ]);

        self::assertFalse($this->staticList->elementsMatch($type));
    }

    public function testGetElementStaticReturnsTheStaticAtThatIndex()
    {
        $elementStatic = $this->staticList->getElementStatic(2);

        self::assertInstanceOf(StaticInterface::class, $elementStatic);
        self::assertSame('third element', $elementStatic->toNative());
    }

    public function testGetElementStaticThrowsExceptionWhenNonIntIndexIsGiven()
    {
        $this->setExpectedException(InvalidArgumentException::class, 'Index must be an int, string given');

        $this->staticList->getElementStatic('my invalid index');
    }

    public function testGetElementStaticThrowsExceptionWhenIndexIsOutOfBounds()
    {
        $this->setExpectedException(OutOfBoundsException::class, 'Index is out of bounds');

        $this->staticList->getElementStatic(100);
    }

    public function testMapReturnsTheMappedArray()
    {
        $mapExpression = $this->prophesize(ExpressionInterface::class);
        $mapExpression->toStatic(Argument::type(EvaluationContextInterface::class))
            ->will(function (array $args) {
                /** @var EvaluationContextInterface $itemEvaluationContext */
                $itemEvaluationContext = $args[0];

                return new TextExpression(
                    sprintf(
                        '%s::%s',
                        $itemEvaluationContext->getVariable('my_index')->toNative(),
                        $itemEvaluationContext->getVariable('my_item')->toNative()
                    )
                );
            });
        /** @var ObjectProphecy|EvaluationContextInterface $evaluationContext */
        $evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        /** @var ObjectProphecy|ExpressionEvaluationContext $evaluationContext */
        $expressionEvaluationContext = $this->prophesize(ExpressionEvaluationContext::class);
        $evaluationContext->createSubExpressionContext(Argument::exact($mapExpression))
            ->willReturn($expressionEvaluationContext);
        $expressionEvaluationContext->createSubScopeContext(Argument::type(StaticBagInterface::class))
            ->will($this->noBind(function (array $args) {
                /** @var StaticBagInterface $variableStaticBag */
                $variableStaticBag = $args[0];
                /** @var ObjectProphecy|ScopeEvaluationContext $itemEvaluationContext */
                $itemEvaluationContext = $this->prophesize(ScopeEvaluationContext::class);
                $itemEvaluationContext->getVariable(Argument::any())
                    ->will(function (array $args) use ($variableStaticBag) {
                        $variableName = $args[0];
                        return $variableStaticBag->getStatic($variableName);
                    });

                return $itemEvaluationContext->reveal();
            }));

        $result = $this->staticList->map(
            'my_item',
            'my_index',
            $mapExpression->reveal(),
            $evaluationContext->reveal()
        );

        self::assertEquals(
            [
                '1::first element',
                '2::2',
                '3::third element'
            ],
            $result->toArray()
        );
    }

    public function testMapArrayReturnsTheMappedArray()
    {
        /** @var ObjectProphecy|EvaluationContextInterface $evaluationContext */
        $evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $evaluationContext->createSubScopeContext(Argument::type(StaticBagInterface::class))
            ->will($this->noBind(function (array $args) {
                /** @var StaticBagInterface $variableStaticBag */
                $variableStaticBag = $args[0];
                /** @var ObjectProphecy|ScopeEvaluationContext $itemEvaluationContext */
                $itemEvaluationContext = $this->prophesize(ScopeEvaluationContext::class);
                $itemEvaluationContext->getVariable(Argument::any())
                    ->will(function (array $args) use ($variableStaticBag) {
                        $variableName = $args[0];
                        return $variableStaticBag->getStatic($variableName);
                    });

                return $itemEvaluationContext->reveal();
            }));

        $result = $this->staticList->mapArray(
            'my_item',
            'my_index',
            function (ScopeEvaluationContext $itemEvaluationContext, StaticInterface $static, $index) {
                return [
                    'ctx' => $itemEvaluationContext,
                    'static' => $static,
                    'index' => $index
                ];
            },
            $evaluationContext->reveal()
        );

        self::assertCount(3, $result);

        self::assertSame(0, $result[0]['index']);
        self::assertEquals(1, $result[0]['ctx']->getVariable('my_index')->toNative());
        self::assertEquals('first element', $result[0]['ctx']->getVariable('my_item')->toNative());
        self::assertSame('first element', $result[0]['static']->toNative());
        self::assertSame(1, $result[1]['index']);
        self::assertEquals(2, $result[1]['ctx']->getVariable('my_index')->toNative());
        self::assertEquals(2, $result[1]['ctx']->getVariable('my_item')->toNative());
        self::assertSame(2, $result[1]['static']->toNative());
        self::assertSame(2, $result[2]['index']);
        self::assertEquals(3, $result[2]['ctx']->getVariable('my_index')->toNative());
        self::assertEquals('third element', $result[2]['ctx']->getVariable('my_item')->toNative());
        self::assertSame('third element', $result[2]['static']->toNative());
    }

    public function testToArrayReturnsTheExpectedNativeArray()
    {
        self::assertEquals(
            ['first element', 2, 'third element'],
            $this->staticList->toArray()
        );
    }

    public function testWithElementsReturnsTheSameListObjectWhenAllAndOnlySpecifiedStaticsAreAlreadyPresent()
    {
        $firstElement = new TextExpression('first element');
        $secondElement = new TextExpression('second element');
        $staticList = new StaticList(
            $this->bagFactory,
            $this->staticExpressionFactory,
            [$firstElement, $secondElement]
        );

        self::assertSame($staticList, $staticList->withElements([$firstElement, $secondElement]));
    }

    public function testWithElementsReturnsANewListWhenAtLeastOneStaticIsNotAlreadyPresent()
    {
        $firstElement = new TextExpression('first element');
        $secondElement = new TextExpression('second element');
        $staticList = new StaticList(
            $this->bagFactory,
            $this->staticExpressionFactory,
            [$firstElement, $secondElement]
        );
        $newElement = new TextExpression('new element');

        $newStaticList = $staticList->withElements([$firstElement, $secondElement, $newElement]);

        self::assertNotSame($staticList, $newStaticList);
        self::assertSame($newElement, $newStaticList->getElementStatic(2));
    }
}
