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

use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use LogicException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BinaryArithmeticExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var BinaryArithmeticExpression
     */
    private $expression;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $leftOperandExpression;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $rightOperandExpression;

    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $subEvaluationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);
        $this->leftOperandExpression = $this->prophesize(ExpressionInterface::class);
        $this->rightOperandExpression = $this->prophesize(ExpressionInterface::class);
        $this->subEvaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $createNumberExpression = function (array $args) {
            /** @var ObjectProphecy|NumberExpression $numberExpression */
            $numberExpression = $this->prophesize(NumberExpression::class);
            $numberExpression->toNative()->willReturn($args[0]);

            return $numberExpression;
        };
        $this->expressionFactory->createNumberExpression(Argument::any())->will(
            function (array $args) use ($createNumberExpression) {
                return $createNumberExpression($args);
            }
        );
    }

    public function testGetType()
    {
        $this->createExpression(BinaryArithmeticExpression::ADD);

        $this->assert($this->expression->getType())->exactlyEquals('binary-arithmetic');
    }

    public function testToStaticCanAddTwoNumbersTogether()
    {
        $this->createExpressionWithOperands(20, BinaryArithmeticExpression::ADD, 7);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(NumberExpression::class);
        $this->assert($resultStatic->toNative())->exactlyEquals(27);
    }

    public function testToStaticCanSubtractTwoNumbersFromEachOther()
    {
        $this->createExpressionWithOperands(27, BinaryArithmeticExpression::SUBTRACT, 3);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(NumberExpression::class);
        $this->assert($resultStatic->toNative())->exactlyEquals(24);
    }

    public function testToStaticCanMultiplyTwoNumbersTogether()
    {
        $this->createExpressionWithOperands(7, BinaryArithmeticExpression::MULTIPLY, 3);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(NumberExpression::class);
        $this->assert($resultStatic->toNative())->exactlyEquals(21);
    }

    public function testToStaticCanDivideANumberByAnother()
    {
        $this->createExpressionWithOperands(20, BinaryArithmeticExpression::DIVIDE, 4);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(NumberExpression::class);
        $this->assert($resultStatic->toNative())->exactlyEquals(5);
    }

    public function testToStaticThrowsLogicExceptionOnDivideByIntegerZero()
    {
        $this->createExpressionWithOperands(20, BinaryArithmeticExpression::DIVIDE, 0);
        
        $this->setExpectedException(LogicException::class, 'Divide by zero - divisor operand should have been assured');

        $this->expression->toStatic($this->evaluationContext->reveal());
    }

    public function testToStaticThrowsLogicExceptionOnDivideByFloatZero()
    {
        $this->createExpressionWithOperands(20, BinaryArithmeticExpression::DIVIDE, .0);

        $this->setExpectedException(LogicException::class, 'Divide by zero - divisor operand should have been assured');

        $this->expression->toStatic($this->evaluationContext->reveal());
    }

    public function testToStaticLogicExceptionOnInvalidOperator()
    {
        $this->createExpressionWithOperands(20, 'invalid_op', 4);

        $this->setExpectedException(
            InvalidArgumentException::class,
            'BinaryArithmeticExpression :: Invalid operator "invalid_op" provided'
        );

        $this->expression->toStatic($this->evaluationContext->reveal());
    }

    /**
     * @param int|float $leftOperandNative
     * @param string $operator
     * @param int|float $rightOperandNative
     */
    private function createExpressionWithOperands($leftOperandNative, $operator, $rightOperandNative)
    {
        /** @var ObjectProphecy|NumberExpression $leftOperandStatic */
        $leftOperandStatic = $this->prophesize(NumberExpression::class);
        $leftOperandStatic->toNative()->willReturn($leftOperandNative);
        $this->leftOperandExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($leftOperandStatic->reveal());
        /** @var ObjectProphecy|NumberExpression $rightOperandStatic */
        $rightOperandStatic = $this->prophesize(NumberExpression::class);
        $rightOperandStatic->toNative()->willReturn($rightOperandNative);
        $this->rightOperandExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($rightOperandStatic->reveal());

        $this->createExpression($operator);
    }

    /**
     * @param string $operator
     */
    private function createExpression($operator)
    {
        $this->expression = new BinaryArithmeticExpression(
            $this->expressionFactory->reveal(),
            $this->leftOperandExpression->reveal(),
            $operator,
            $this->rightOperandExpression->reveal()
        );

        $this->evaluationContext->createSubExpressionContext(Argument::is($this->expression))
            ->willReturn($this->subEvaluationContext->reveal());
    }
}
