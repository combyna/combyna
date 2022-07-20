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

use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ConversionExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConversionExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var ConversionExpression
     */
    private $expression;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $operandExpression;

    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $subEvaluationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);
        $this->operandExpression = $this->prophesize(ExpressionInterface::class);
        $this->subEvaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $createBooleanExpression = function (array $args) {
            $booleanExpression = $this->prophesize(BooleanExpression::class);
            $booleanExpression->getType()->willReturn(BooleanExpression::TYPE);
            $booleanExpression->toNative()->willReturn($args[0]);

            return $booleanExpression;
        };
        $this->expressionFactory->createBooleanExpression(Argument::any())->will(
            function (array $args) use ($createBooleanExpression) {
                return $createBooleanExpression($args);
            }
        );
        $createNumberExpression = function (array $args) {
            $numberExpression = $this->prophesize(NumberExpression::class);
            $numberExpression->getType()->willReturn(NumberExpression::TYPE);
            $numberExpression->toNative()->willReturn($args[0]);

            return $numberExpression;
        };
        $this->expressionFactory->createNumberExpression(Argument::any())->will(
            function (array $args) use ($createNumberExpression) {
                return $createNumberExpression($args);
            }
        );
        $createTextExpression = function (array $args) {
            $textExpression = $this->prophesize(TextExpression::class);
            $textExpression->getType()->willReturn(TextExpression::TYPE);
            $textExpression->toNative()->willReturn($args[0]);

            return $textExpression;
        };
        $this->expressionFactory->createTextExpression(Argument::any())->will(
            function (array $args) use ($createTextExpression) {
                return $createTextExpression($args);
            }
        );
    }

    public function testGetType()
    {
        $this->createExpression(ConversionExpression::NUMBER_TO_TEXT);

        static::assertSame('conversion', $this->expression->getType());
    }

    public function testToStaticConvertsAnIntegerToText()
    {
        $this->createExpressionWithNumberOperand(21, ConversionExpression::NUMBER_TO_TEXT);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(TextExpression::class, $resultStatic);
        static::assertSame('21', $resultStatic->toNative());
    }

    public function testToStaticConvertsAFloatToText()
    {
        $this->createExpressionWithNumberOperand(1001.423, ConversionExpression::NUMBER_TO_TEXT);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(TextExpression::class, $resultStatic);
        static::assertSame('1001.423', $resultStatic->toNative());
    }

    public function testToStaticConvertsAStringContainingAnIntegerToAnInteger()
    {
        $this->createExpressionWithTextOperand('321', ConversionExpression::TEXT_TO_NUMBER);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(NumberExpression::class, $resultStatic);
        static::assertSame(321, $resultStatic->toNative());
    }

    public function testToStaticConvertsAStringContainingAFloatToAFloat()
    {
        $this->createExpressionWithTextOperand('987.654', ConversionExpression::TEXT_TO_NUMBER);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(NumberExpression::class, $resultStatic);
        static::assertSame(987.654, $resultStatic->toNative());
    }

    public function testToStaticConvertsAStringThatDoesNotContainANumberToIntegerZero()
    {
        $this->createExpressionWithTextOperand('not a number', ConversionExpression::TEXT_TO_NUMBER);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(NumberExpression::class, $resultStatic);
        static::assertSame(0, $resultStatic->toNative());
    }

    public function testToStaticThrowsForNumberToTextConversionWhenOperandIsNotANumberExpression()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'ConversionExpression :: Input can only evaluate to a number static ' .
            'for number->text conversion, but got a(n) "text"'
        );

        $this->createExpressionWithTextOperand('not a number', ConversionExpression::NUMBER_TO_TEXT);

        $this->expression->toStatic($this->evaluationContext->reveal());
    }

    public function testToStaticThrowsForTextToNumberConversionWhenOperandIsNotATextExpression()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(
            'ConversionExpression :: Input can only evaluate to a text static ' .
            'for text->number conversion, but got a(n) "number"'
        );

        $this->createExpressionWithNumberOperand(333, ConversionExpression::TEXT_TO_NUMBER);

        $this->expression->toStatic($this->evaluationContext->reveal());
    }

    /**
     * @param int|float $operandNative
     * @param string $conversion
     */
    private function createExpressionWithNumberOperand($operandNative, $conversion)
    {
        $operandStatic = $this->prophesize(NumberExpression::class);
        $operandStatic->getType()->willReturn(NumberExpression::TYPE);
        $operandStatic->toNative()->willReturn($operandNative);
        $this->operandExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($operandStatic->reveal());

        $this->createExpression($conversion);
    }

    /**
     * @param string $operandNative
     * @param string $operator
     */
    private function createExpressionWithTextOperand($operandNative, $operator)
    {
        $operandStatic = $this->prophesize(TextExpression::class);
        $operandStatic->getType()->willReturn(TextExpression::TYPE);
        $operandStatic->toNative()->willReturn($operandNative);
        $this->operandExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($operandStatic->reveal());

        $this->createExpression($operator);
    }

    /**
     * @param string $conversion
     */
    private function createExpression($conversion)
    {
        $this->expression = new ConversionExpression(
            $this->expressionFactory->reveal(),
            $this->operandExpression->reveal(),
            $conversion
        );

        $this->evaluationContext->createSubExpressionContext(Argument::is($this->expression))
            ->willReturn($this->subEvaluationContext->reveal());
    }
}
