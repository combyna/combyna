<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Expression\Validation;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Config\Act\HomeNode;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionTypeNode;
use Combyna\Component\Program\Validation\Validator\NodeValidator;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BinaryArithmeticExpressionIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionIntegratedTest extends TestCase
{
    /**
     * @var AppNode
     */
    private $appNode;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var NodeValidator
     */
    private $nodeValidator;

    public function setUp()
    {
        global $combynaBootstrap; // Use the one from bootstrap.php so that all the test plugins are loaded etc.
        $this->container = $combynaBootstrap->getContainer();

        $this->environmentNode = new EnvironmentNode();
        $this->appNode = new AppNode(
            $this->environmentNode,
            [],
            [],
            new HomeNode('app', 'home', new ExpressionBagNode([])),
            [],
            []
        );
        $this->nodeValidator = $this->container->get('combyna.program.node_validator');
    }

    public function testResultTypeIsNumber()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new NumberExpressionNode(10),
            BinaryArithmeticExpression::MULTIPLY,
            new NumberExpressionNode(3)
        );
        $rootValidationContext = $this->nodeValidator->validate($expressionNode, $this->appNode);

        $type = $rootValidationContext->getExpressionResultType($expressionNode);

        $this->assert($type)->isAnInstanceOf(StaticType::class);
        $this->assert($type->getSummary())->exactlyEquals('number');
    }

    public function testLeftOperandMustEvaluateToANumber()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new TextExpressionNode('not a number'),
            BinaryArithmeticExpression::MULTIPLY,
            new NumberExpressionNode(3)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic]' .
            ' - left operand would get [text], expects [number]'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testRightOperandMustEvaluateToANumber()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new NumberExpressionNode(3),
            BinaryArithmeticExpression::MULTIPLY,
            new TextExpressionNode('not a number')
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic]' .
            ' - right operand would get [text], expects [number]'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForDivisionDivisorCanBeANonZeroConstant()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new NumberExpressionNode(10),
            BinaryArithmeticExpression::DIVIDE,
            new NumberExpressionNode(2)
        );

        // No exception expected

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForDivisionDivisorMustNotBeConstantZero()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new NumberExpressionNode(3),
            BinaryArithmeticExpression::DIVIDE,
            new NumberExpressionNode(0)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic]' .
            ' - Division by zero'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForDivisionDivisorMustBeAssuredNonZero()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new NumberExpressionNode(3),
            BinaryArithmeticExpression::DIVIDE,
            new BinaryArithmeticExpressionNode(
                new NumberExpressionNode(3),
                BinaryArithmeticExpression::ADD,
                new NumberExpressionNode(2)
            )
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic]' .
            ' - divisor (right operand) expects "assured", got "binary-arithmetic"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testArithmeticOperatorMustBeValid()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new NumberExpressionNode(3),
            'not-a-valid-op',
            new NumberExpressionNode(2)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic]' .
            ' - Invalid operator "not-a-valid-op" provided'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testTheOperandsMustBeValid()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new UnknownExpressionTypeNode('left-unknown-type'),
            BinaryArithmeticExpression::ADD,
            new UnknownExpressionTypeNode('right-unknown-type')
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic]' .
            ' - left operand would get [unknown<Expression type "left-unknown-type">], expects [number]. :: ' .

            'ACT node [detached].[binary-arithmetic]' .
            ' - right operand would get [unknown<Expression type "right-unknown-type">], expects [number]. :: ' .

            'ACT node [detached].[binary-arithmetic].[unknown]' .
            ' - Expression is of unknown type "left-unknown-type". :: ' .

            'ACT node [detached].[binary-arithmetic].[unknown]' .
            ' - Expression is of unknown type "right-unknown-type"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }
}
