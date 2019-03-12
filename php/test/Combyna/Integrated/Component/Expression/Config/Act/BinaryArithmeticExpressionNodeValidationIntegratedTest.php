<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Expression;

use Combyna\Component\App\Config\Act\AppNode;
use Combyna\Component\App\Config\Act\HomeNode;
use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\Config\Act\Assurance\NonZeroNumberAssuranceNode;
use Combyna\Component\Expression\Config\Act\AssuredExpressionNode;
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\ConversionExpressionNode;
use Combyna\Component\Expression\Config\Act\GuardExpressionNode;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Program\Validation\Validator\NodeValidator;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\ValidationFactory;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class BinaryArithmeticExpressionNodeValidationIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionNodeValidationIntegratedTest extends TestCase
{
    /**
     * @var AppNode
     */
    private $appNode;

    /**
     * @var BagFactory
     */
    private $bagFactory;

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

    /**
     * @var ValidationFactory
     */
    private $validationFactory;

    public function setUp()
    {
        global $combynaBootstrap; // Use the one from bootstrap.php so that all the test plugins are loaded etc.
        $this->container = $combynaBootstrap->createContainer();

        $staticExpressionFactory = $this->container->get('combyna.expression.static_factory');
        $this->validationFactory = $this->container->get('combyna.validator.factory');
        $this->bagFactory = new BagFactory($staticExpressionFactory);
        $this->environmentNode = new EnvironmentNode([]);
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

    public function testAllowsDivisionByNonZeroStatic()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new ConversionExpressionNode(
                new TextExpressionNode('10'),
                ConversionExpression::TEXT_TO_NUMBER
            ),
            BinaryArithmeticExpression::DIVIDE,
            new NumberExpressionNode(2)
        );

        // Should not throw, as divisor can statically be read as const(2), which is non-zero
        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testAllowsDivisionByGuardedAssuredExpression()
    {
        $expressionNode = new GuardExpressionNode(
            [
                new NonZeroNumberAssuranceNode(
                    // This would normally be a more complex expression
                    new NumberExpressionNode(21),
                    'myNonZeroDivisor'
                )
            ],
            new BinaryArithmeticExpressionNode(
                new NumberExpressionNode(30),
                BinaryArithmeticExpression::DIVIDE,
                new AssuredExpressionNode('myNonZeroDivisor')
            ),
            new TextExpressionNode(' - it was zero, oops')
        );

        // Should not throw, as divisor is assured as non-zero
        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testDoesNotAllowDivisionByIntegerZeroStatic()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new ConversionExpressionNode(
                new TextExpressionNode('10'),
                ConversionExpression::TEXT_TO_NUMBER
            ),
            BinaryArithmeticExpression::DIVIDE,
            new NumberExpressionNode(0)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic] - Division by zero'
        );

        // Should throw, as divisor can statically be read as const(0)
        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testDoesNotAllowDivisionByFloatZeroStatic()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new ConversionExpressionNode(
                new TextExpressionNode('10'),
                ConversionExpression::TEXT_TO_NUMBER
            ),
            BinaryArithmeticExpression::DIVIDE,
            new NumberExpressionNode(.0)
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic] - Division by zero'
        );

        // Should throw, as divisor can statically be read as const(0.0)
        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testDoesNotAllowDivisionByUnassuredExpression()
    {
        $expressionNode = new BinaryArithmeticExpressionNode(
            new ConversionExpressionNode(
                new TextExpressionNode('10'),
                ConversionExpression::TEXT_TO_NUMBER
            ),
            BinaryArithmeticExpression::DIVIDE,
            new BinaryArithmeticExpressionNode(
                new NumberExpressionNode(21),
                BinaryArithmeticExpression::ADD,
                new NumberExpressionNode(4)
            )
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[binary-arithmetic] - divisor (right operand) expects "assured", got "binary-arithmetic"'
        );

        // Should throw, as divisor is not assured and not a constant
        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testDoesNotAllowDivisionByAssuredExpressionWithWrongAssurance()
    {
        $wrongAssuranceNode = $this->prophesize(AssuranceNodeInterface::class);
        $wrongAssuranceNode->buildBehaviourSpec(Argument::any())->willReturn(null);
        $wrongAssuranceNode->getAssuredStaticName()->willReturn('myAssuredStatic');
        $wrongAssuranceNode->getConstraint()->willReturn('the-wrong-constraint');
        $wrongAssuranceNode->makesQuery(Argument::any())->willReturn(false);

        $expressionNode = new GuardExpressionNode(
            [
                $wrongAssuranceNode->reveal()
            ],
            new BinaryArithmeticExpressionNode(
                new NumberExpressionNode(30),
                BinaryArithmeticExpression::DIVIDE,
                new AssuredExpressionNode('myAssuredStatic')
            ),
            new TextExpressionNode(' - it did not meet the assurance, oops')
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[guard].[binary-arithmetic] - divisor (right operand) expects "non-zero-number" constraint, got "the-wrong-constraint"'
        );

        // Should throw, as divisor is not assured to be non-zero
        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }
}
