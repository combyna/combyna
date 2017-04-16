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

use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BinaryArithmeticExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $leftOperandExpressionNode;

    /**
     * @var BinaryArithmeticExpressionNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $rightOperandExpressionNode;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $subValidationContext;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->leftOperandExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->rightOperandExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->subValidationContext = $this->prophesize(ValidationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->leftOperandExpressionNode->validate(Argument::is($this->subValidationContext->reveal()))
            ->willReturn(null);
        $this->rightOperandExpressionNode->validate(Argument::is($this->subValidationContext->reveal()))
            ->willReturn(null);
    }

    public function testGetLeftOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->assert($this->node->getLeftOperandExpression())->isTheSameAs($this->leftOperandExpressionNode->reveal());
    }

    public function testGetOperatorFetchesTheOperator()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::MULTIPLY);

        $this->assert($this->node->getOperator())->exactlyEquals(BinaryArithmeticExpression::MULTIPLY);
    }

    public function testGetRightOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->assert($this->node->getRightOperandExpression())->isTheSameAs($this->rightOperandExpressionNode->reveal());
    }

    public function testGetType()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->assert($this->node->getType())->exactlyEquals('binary-arithmetic');
    }

    public function testGetResultTypeReturnsAStaticNumberType()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $type = $this->node->getResultType($this->validationContext->reveal());

        $this->assert($type)->isAnInstanceOf(StaticType::class);
        $this->assert($type->getSummary())->exactlyEquals('number');
    }

    public function testValidateValidatesTheLeftOperandInASubValidationContext()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->node->validate($this->validationContext->reveal());

        $this->leftOperandExpressionNode->validate(Argument::is($this->subValidationContext))
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesTheRightOperandInASubValidationContext()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->node->validate($this->validationContext->reveal());

        $this->rightOperandExpressionNode->validate(Argument::is($this->subValidationContext))
            ->shouldHaveBeenCalled();
    }

    public function testValidateChecksTheLeftOperandCanOnlyEvaluateToANumber()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertResultType(
            Argument::is($this->leftOperandExpressionNode->reveal()),
            Argument::any(),
            'left operand'
        )
            ->shouldHaveBeenCalled()
            ->shouldHave($this->noBind(function (array $calls) {
                /** @var Call[] $calls */
                list(, $type) = $calls[0]->getArguments();
                /** @var StaticType $type */
                $this->assert($type)->isAnInstanceOf(StaticType::class);
                $this->assert($type->getSummary())->exactlyEquals('number');
            }));
    }

    public function testValidateForNonDivisionChecksTheRightOperandCanOnlyEvaluateToANumber()
    {
        $this->createExpressionNode(BinaryArithmeticExpression::ADD);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertResultType(
            Argument::is($this->rightOperandExpressionNode->reveal()),
            Argument::any(),
            'right operand'
        )
            ->shouldHaveBeenCalled()
            ->shouldHave($this->noBind(function (array $calls) {
                /** @var Call[] $calls */
                list(, $type) = $calls[0]->getArguments();
                /** @var StaticType $type */
                $this->assert($type)->isAnInstanceOf(StaticType::class);
                $this->assert($type->getSummary())->exactlyEquals('number');
            }));
        $this->subValidationContext->assertAssured(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    public function testValidateAddsDivideByZeroViolationForStaticDivisorOfZero()
    {
        $this->rightOperandExpressionNode = $this->prophesize(NumberExpressionNode::class);
        $this->rightOperandExpressionNode->toNative()->willReturn(0);
        $this->rightOperandExpressionNode->validate(Argument::cetera())->willReturn(null);
        $this->createExpressionNode(BinaryArithmeticExpression::DIVIDE);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->addDivisionByZeroViolation()
            ->shouldHaveBeenCalled();
        $this->subValidationContext->assertAssured(Argument::cetera())
            ->shouldNotHaveBeenCalled();
    }

    /**
     * @param string $operator
     */
    private function createExpressionNode($operator)
    {
        $this->node = new BinaryArithmeticExpressionNode(
            $this->leftOperandExpressionNode->reveal(),
            $operator,
            $this->rightOperandExpressionNode->reveal()
        );

        $this->validationContext->createSubActNodeContext(Argument::is($this->node))
            ->willReturn($this->subValidationContext->reveal());
    }
}
