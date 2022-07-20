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
use Combyna\Component\Bag\Expression\Evaluation\BagEvaluationContextFactoryInterface;
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
use Combyna\Component\Validator\Context\ValidationContextInterface;
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
        $bagEvaluationContextFactory = $this->prophesize(BagEvaluationContextFactoryInterface::class);
        $this->bagFactory = new BagFactory($this->staticExpressionFactory, $bagEvaluationContextFactory->reveal());

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
        static::assertSame('first element,2,third element', $this->staticList->concatenate(','));
    }

    public function testListIsCountable()
    {
        static::assertSame(3, count($this->staticList));
    }

    public function testElementsMatchReturnsTrueForATypeThatMatchesAllTheElements()
    {
        $validationContext = $this->prophesize(ValidationContextInterface::class);
        $type = new MultipleType(
            [
                new StaticType(TextExpression::class, $validationContext->reveal()),
                new StaticType(NumberExpression::class, $validationContext->reveal())
            ],
            $validationContext->reveal()
        );

        static::assertTrue($this->staticList->elementsMatch($type));
    }

    public function testElementsMatchReturnsFalseForATypeThatMatchesNoneOfTheElements()
    {
        $validationContext = $this->prophesize(ValidationContextInterface::class);
        $type = new StaticType(BooleanExpression::class, $validationContext->reveal());

        static::assertFalse($this->staticList->elementsMatch($type));
    }

    public function testElementsMatchReturnsFalseForATypeThatMatchesOnlySomeOfTheElements()
    {
        $validationContext = $this->prophesize(ValidationContextInterface::class);
        $type = new MultipleType(
            [
                new StaticType(TextExpression::class, $validationContext->reveal()),
                new StaticType(BooleanExpression::class, $validationContext->reveal())
            ],
            $validationContext->reveal()
        );

        static::assertFalse($this->staticList->elementsMatch($type));
    }

    public function testGetElementStaticReturnsTheStaticAtThatIndex()
    {
        $elementStatic = $this->staticList->getElementStatic(2);

        static::assertInstanceOf(StaticInterface::class, $elementStatic);
        static::assertSame('third element', $elementStatic->toNative());
    }

    public function testGetElementStaticThrowsExceptionWhenNonIntIndexIsGiven()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Index must be an int, string given');

        $this->staticList->getElementStatic('my invalid index');
    }

    public function testGetElementStaticThrowsExceptionWhenIndexIsOutOfBounds()
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Index is out of bounds');

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

        static::assertEquals(
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

        static::assertCount(3, $result);

        static::assertSame(0, $result[0]['index']);
        static::assertEquals(1, $result[0]['ctx']->getVariable('my_index')->toNative());
        static::assertEquals('first element', $result[0]['ctx']->getVariable('my_item')->toNative());
        static::assertSame('first element', $result[0]['static']->toNative());
        static::assertSame(1, $result[1]['index']);
        static::assertEquals(2, $result[1]['ctx']->getVariable('my_index')->toNative());
        static::assertEquals(2, $result[1]['ctx']->getVariable('my_item')->toNative());
        static::assertSame(2, $result[1]['static']->toNative());
        static::assertSame(2, $result[2]['index']);
        static::assertEquals(3, $result[2]['ctx']->getVariable('my_index')->toNative());
        static::assertEquals('third element', $result[2]['ctx']->getVariable('my_item')->toNative());
        static::assertSame('third element', $result[2]['static']->toNative());
    }

    public function testToArrayReturnsTheExpectedNativeArray()
    {
        static::assertEquals(
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

        static::assertSame($staticList, $staticList->withElements([$firstElement, $secondElement]));
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

        static::assertNotSame($staticList, $newStaticList);
        static::assertSame($newElement, $newStaticList->getElementStatic(2));
    }
}
