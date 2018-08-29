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
use Combyna\Component\Bag\Config\Act\ExpressionListNode;
use Combyna\Component\Bag\Config\Act\FixedStaticBagModelNode;
use Combyna\Component\Bag\Config\Act\FixedStaticDefinitionNode;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\LibraryNode;
use Combyna\Component\Environment\Config\Act\NativeFunctionNode;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\Config\Act\Assurance\AssuranceNodeInterface;
use Combyna\Component\Expression\Config\Act\Assurance\NonZeroNumberAssuranceNode;
use Combyna\Component\Expression\Config\Act\AssuredExpressionNode;
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\BooleanExpressionNode;
use Combyna\Component\Expression\Config\Act\ComparisonExpressionNode;
use Combyna\Component\Expression\Config\Act\ConcatenationExpressionNode;
use Combyna\Component\Expression\Config\Act\ConditionalExpressionNode;
use Combyna\Component\Expression\Config\Act\ConversionExpressionNode;
use Combyna\Component\Expression\Config\Act\FunctionExpressionNode;
use Combyna\Component\Expression\Config\Act\GuardExpressionNode;
use Combyna\Component\Expression\Config\Act\ListExpressionNode;
use Combyna\Component\Expression\Config\Act\MapExpressionNode;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Expression\Config\Act\TranslationExpressionNode;
use Combyna\Component\Expression\Config\Act\VariableExpressionNode;
use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Program\Validation\Validator\NodeValidator;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\ValidationFactory;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ExpressionValidationIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ExpressionValidationIntegratedTest extends TestCase
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
        $this->container = $combynaBootstrap->getContainer();

        $staticExpressionFactory = $this->container->get('combyna.expression.static_factory');
        $this->validationFactory = $this->container->get('combyna.validator.factory');
        $this->bagFactory = new BagFactory($staticExpressionFactory);
        $this->environmentNode = new EnvironmentNode([
            new LibraryNode(
                'text',
                'A library for processing of text data',
                [],
                [
                    new NativeFunctionNode(
                        'text',
                        'length',
                        new FixedStaticBagModelNode(
                            [
                                new FixedStaticDefinitionNode(
                                    'textString',
                                    new StaticType(TextExpression::class)
                                )
                            ]
                        ),
                        new StaticType(NumberExpression::class)
                    )
                ]
            )
        ]);
        $this->appNode = new AppNode(
            $this->environmentNode,
            [],
            [],
            new HomeNode('app', 'home', new ExpressionBagNode([])),
            [],
            []
        );
        $this->nodeValidator = $this->container->get('combyna.program.node_validator');

        $this->environmentNode->installNativeFunction(
            'text',
            'length',
            function (StaticBagInterface $argumentBag) {
                // ...
            }
        );
    }

    public function testDoesNotAllowExpressionsWithInvalidResultTypes()
    {
        $expressionNode = new ConditionalExpressionNode(
            new ComparisonExpressionNode(
                new ComparisonExpressionNode(
                    new FunctionExpressionNode(
                        'text',
                        'length',
                        new ExpressionBagNode([
                            'textString' => new NumberExpressionNode(21) // Wrong type
                        ])
                    ),
                    ComparisonExpression::EQUAL,
                    new NumberExpressionNode(21)
                ),
                ComparisonExpression::EQUAL,
                new TextExpressionNode('this should be a boolean') // Wrong type
            ),
            new TextExpressionNode('wrong'),
            new ConcatenationExpressionNode(
                new ListExpressionNode(
                    new ExpressionListNode([
                        new BooleanExpressionNode(false), // Wrong type
                        new BinaryArithmeticExpressionNode(
                            new ConversionExpressionNode(
                                new TextExpressionNode('7'),
                                ConversionExpression::TEXT_TO_NUMBER
                            ),
                            BinaryArithmeticExpression::MULTIPLY,
                            new TextExpressionNode('this should also be a number') // Wrong type
                        ),
                        new TextExpressionNode(' would be my result'),
                        new MapExpressionNode(
                            new ListExpressionNode(
                                new ExpressionListNode([
                                    new TextExpressionNode('my element')
                                ])
                            ),
                            'my_var',
                            'my_index',
                            new VariableExpressionNode('my_var')
                        ),
                        new TranslationExpressionNode(
                            'my.key',
                            new ExpressionBagNode([
                                'my_param' => new TextExpressionNode('1001')
                            ])
                        )
                    ])
                )
            )
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[conditional].[comparison]' .
            ' - operands "boolean" and "text" do not both match just one of the provided allowed result types, ' .
            'allowed types are: "boolean", "number", "text". :: ' .
            'ACT node [detached].[conditional].[comparison].[comparison].[function]' .
            ' - parameter textString would get [number], expects [text]. :: ' .
            'ACT node [detached].[conditional].[concatenation]' .
            ' - operand list would get [list<boolean|number|text|list<text>|text>], ' .
            'expects [list<text|number>]. :: ' .
            'ACT node [detached].[conditional].[concatenation].[list].[expression-list].[binary-arithmetic]' .
            ' - right operand would get [text], expects [number]'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
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

    public function testDoesNotAllowDivisionByZeroStatic()
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
