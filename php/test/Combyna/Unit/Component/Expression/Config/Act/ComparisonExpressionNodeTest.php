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
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\Config\Act\ComparisonExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ComparisonExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpressionNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $leftOperandExpressionNode;

    /**
     * @var ComparisonExpressionNode
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
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        $this->assert($this->node->getLeftOperandExpression())->isTheSameAs($this->leftOperandExpressionNode->reveal());
    }

    public function testGetOperatorFetchesTheOperator()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        $this->assert($this->node->getOperator())->isTheSameAs(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);
    }

    public function testGetRightOperandExpressionFetchesTheExpression()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        $this->assert($this->node->getRightOperandExpression())->isTheSameAs($this->rightOperandExpressionNode->reveal());
    }

    public function testGetType()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL_CASE_INSENSITIVE);

        $this->assert($this->node->getType())->exactlyEquals('comparison');
    }

    public function testGetResultTypeReturnsAStaticNumberType()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL);

        $type = $this->node->getResultType($this->validationContext->reveal());

        $this->assert($type)->isAnInstanceOf(StaticType::class);
        $this->assert($type->getSummary())->exactlyEquals('boolean');
    }

    public function testValidateValidatesTheLeftOperandInASubValidationContext()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL);

        $this->node->validate($this->validationContext->reveal());

        $this->leftOperandExpressionNode->validate(Argument::is($this->subValidationContext))
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesTheRightOperandInASubValidationContext()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL);

        $this->node->validate($this->validationContext->reveal());

        $this->rightOperandExpressionNode->validate(Argument::is($this->subValidationContext))
            ->shouldHaveBeenCalled();
    }

    public function testValidateForEqualComparisonChecksOperandsCorrectly()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertPossibleMatchingResultTypes(
            Argument::is($this->leftOperandExpressionNode->reveal()),
            'left operand',
            Argument::is($this->rightOperandExpressionNode->reveal()),
            'right operand',
            [
                new StaticType(BooleanExpression::class),
                new StaticType(NumberExpression::class),
                new StaticType(TextExpression::class)
            ]
        )
            ->shouldHaveBeenCalled();
    }

    public function testValidateForCaseInsensitiveEqualComparisonChecksOperandsCorrectly()
    {
        $this->createExpressionNode(ComparisonExpression::EQUAL_CASE_INSENSITIVE);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertPossibleMatchingResultTypes(
            Argument::is($this->leftOperandExpressionNode->reveal()),
            'left operand',
            Argument::is($this->rightOperandExpressionNode->reveal()),
            'right operand',
            [
                new StaticType(TextExpression::class)
            ]
        )
            ->shouldHaveBeenCalled();
    }

    public function testValidateForGreaterThanComparisonChecksOperandsCorrectly()
    {
        $this->createExpressionNode(ComparisonExpression::GREATER_THAN);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertPossibleMatchingResultTypes(
            Argument::is($this->leftOperandExpressionNode->reveal()),
            'left operand',
            Argument::is($this->rightOperandExpressionNode->reveal()),
            'right operand',
            [
                new StaticType(NumberExpression::class)
            ]
        )
            ->shouldHaveBeenCalled();
    }

    public function testValidateForLessThanComparisonChecksOperandsCorrectly()
    {
        $this->createExpressionNode(ComparisonExpression::LESS_THAN);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertPossibleMatchingResultTypes(
            Argument::is($this->leftOperandExpressionNode->reveal()),
            'left operand',
            Argument::is($this->rightOperandExpressionNode->reveal()),
            'right operand',
            [
                new StaticType(NumberExpression::class)
            ]
        )
            ->shouldHaveBeenCalled();
    }

    public function testValidateForUnequalComparisonChecksOperandsCorrectly()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertPossibleMatchingResultTypes(
            Argument::is($this->leftOperandExpressionNode->reveal()),
            'left operand',
            Argument::is($this->rightOperandExpressionNode->reveal()),
            'right operand',
            [
                new StaticType(BooleanExpression::class),
                new StaticType(NumberExpression::class),
                new StaticType(TextExpression::class)
            ]
        )
            ->shouldHaveBeenCalled();
    }

    public function testValidateForCaseInsensitiveUnequalComparisonChecksOperandsCorrectly()
    {
        $this->createExpressionNode(ComparisonExpression::UNEQUAL_CASE_INSENSITIVE);

        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertPossibleMatchingResultTypes(
            Argument::is($this->leftOperandExpressionNode->reveal()),
            'left operand',
            Argument::is($this->rightOperandExpressionNode->reveal()),
            'right operand',
            [
                new StaticType(TextExpression::class)
            ]
        )
            ->shouldHaveBeenCalled();
    }

    public function testValidateThrowsExceptionWhenInvalidOperatorProvided()
    {
        $this->createExpressionNode('invalid operator');

        $this->setExpectedException(
            InvalidArgumentException::class,
            'ComparisonExpressionNode :: Invalid operator "invalid operator" provided'
        );

        $this->node->validate($this->validationContext->reveal());
    }

    /**
     * @param string $operator
     */
    private function createExpressionNode($operator)
    {
        $this->node = new ComparisonExpressionNode(
            $this->leftOperandExpressionNode->reveal(),
            $operator,
            $this->rightOperandExpressionNode->reveal()
        );

        $this->validationContext->createSubActNodeContext(Argument::is($this->node))
            ->willReturn($this->subValidationContext->reveal());
    }
}
