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
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\Config\Act\BooleanExpressionNode;
use Combyna\Component\Expression\Config\Act\ComparisonExpressionNode;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionTypeNode;
use Combyna\Component\Program\Validation\Validator\NodeValidator;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ComparisonExpressionIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpressionIntegratedTest extends TestCase
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

    public function testResultTypeIsBoolean()
    {
        $expressionNode = new ComparisonExpressionNode(
            new NumberExpressionNode(10),
            ComparisonExpression::EQUAL,
            new NumberExpressionNode(3)
        );
        $rootValidationContext = $this->nodeValidator->validate($expressionNode, $this->appNode);

        $type = $rootValidationContext->getExpressionResultType($expressionNode);

        $this->assert($type)->isAnInstanceOf(StaticType::class);
        $this->assert($type->getSummary())->exactlyEquals('boolean');
    }

    public function testForEqualOperationOperandsMustBothEvaluateToTheSameScalarType()
    {
        $expressionNode = new ComparisonExpressionNode(
            new TextExpressionNode('not a number'),
            ComparisonExpression::EQUAL,
            new NumberExpressionNode(3)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - operands "text" and "number" do not both match just one of the provided allowed result types' .
            ', allowed types are: "boolean", "number", "text"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForCaseInsensitiveEqualOperationOperandsMustBothEvaluateToText()
    {
        $expressionNode = new ComparisonExpressionNode(
            new BooleanExpressionNode(true), // Not text
            ComparisonExpression::EQUAL_CASE_INSENSITIVE,
            new NumberExpressionNode(3) // Not text
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - operands "boolean" and "number" do not both match just one of the provided allowed result types' .
            ', allowed types are: "text"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForGreaterThanOperationOperandsMustBothEvaluateToNumbers()
    {
        $expressionNode = new ComparisonExpressionNode(
            new TextExpressionNode('not a number'),
            ComparisonExpression::GREATER_THAN,
            new BooleanExpressionNode(false)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - operands "text" and "boolean" do not both match just one of the provided allowed result types' .
            ', allowed types are: "number"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForLessThanOperationOperandsMustBothEvaluateToNumbers()
    {
        $expressionNode = new ComparisonExpressionNode(
            new TextExpressionNode('not a number'),
            ComparisonExpression::LESS_THAN,
            new BooleanExpressionNode(false)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - operands "text" and "boolean" do not both match just one of the provided allowed result types' .
            ', allowed types are: "number"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForUnequalOperationOperandsMustBothEvaluateToTheSameScalarType()
    {
        $expressionNode = new ComparisonExpressionNode(
            new TextExpressionNode('not a number'),
            ComparisonExpression::UNEQUAL,
            new NumberExpressionNode(3)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - operands "text" and "number" do not both match just one of the provided allowed result types' .
            ', allowed types are: "boolean", "number", "text"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testForCaseInsensitiveUnequalOperationOperandsMustBothEvaluateToText()
    {
        $expressionNode = new ComparisonExpressionNode(
            new BooleanExpressionNode(true), // Not text
            ComparisonExpression::UNEQUAL_CASE_INSENSITIVE,
            new NumberExpressionNode(3) // Not text
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - operands "boolean" and "number" do not both match just one of the provided allowed result types' .
            ', allowed types are: "text"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testArithmeticOperatorMustBeValid()
    {
        $expressionNode = new ComparisonExpressionNode(
            new NumberExpressionNode(3),
            'not-a-valid-op',
            new NumberExpressionNode(2)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - Invalid operator "not-a-valid-op" provided'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testTheOperandsMustBeValid()
    {
        $expressionNode = new ComparisonExpressionNode(
            new UnknownExpressionTypeNode('left-unknown-type'),
            ComparisonExpression::EQUAL,
            new UnknownExpressionTypeNode('right-unknown-type')
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[comparison]' .
            ' - operands "unknown<Expression type "left-unknown-type">" and "unknown<Expression type "right-unknown-type">" do not both match just one of the provided allowed result types, allowed types are: "boolean", "number", "text". :: ' .

            'ACT node [detached].[comparison].[unknown]' .
            ' - Expression is of unknown type "left-unknown-type". :: ' .

            'ACT node [detached].[comparison].[unknown]' .
            ' - Expression is of unknown type "right-unknown-type"'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }
}
