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

use Combyna\Component\Bag\BagFactory;
use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Bag\Config\Act\ExpressionListNode;
use Combyna\Component\Bag\FixedStaticBagModel;
use Combyna\Component\Bag\FixedStaticDefinition;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Environment\Config\Act\LibraryNode;
use Combyna\Component\Environment\Config\Act\NativeFunctionNode;
use Combyna\Component\Environment\Library\NativeFunction;
use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\ComparisonExpression;
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
use Combyna\Component\Expression\StaticExpressionFactory;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Component\Validator\ValidationFactory;
use Combyna\Component\Validator\Validator;
use Combyna\Harness\TestCase;
use Combyna\Parameter\ParameterBagModel;

/**
 * Class ValidationIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ValidationIntegratedTest extends TestCase
{
    /**
     * @var BagFactory
     */
    private $bagFactory;

    /**
     * @var EnvironmentNode
     */
    private $environmentNode;

    /**
     * @var Validator
     */
    private $expressionValidator;

    /**
     * @var ValidationFactory
     */
    private $validationFactory;

    public function setUp()
    {
        $staticExpressionFactory = new StaticExpressionFactory();
        $this->validationFactory = new ValidationFactory();
        $this->bagFactory = new BagFactory($staticExpressionFactory, $this->validationFactory);
        $this->environmentNode = new EnvironmentNode([
            new LibraryNode(
                'text',
                'A library for processing of text data',
                [
                    new NativeFunctionNode('text', 'length')
                ]
            )
        ]);
        $this->expressionValidator = new Validator($this->validationFactory);

        $this->environmentNode->installNativeFunction(
            'text',
            new NativeFunction(
                'length',
                new ParameterBagModel(
                    new FixedStaticBagModel(
                        $this->bagFactory,
                        $this->validationFactory,
                        [
                            new FixedStaticDefinition(
                                $this->validationFactory,
                                'textString',
                                new StaticType(TextExpression::class)
                            )
                        ]
                    )
                ),
                function (StaticBagInterface $argumentBag) {
                    // ...
                },
                new StaticType(NumberExpression::class)
            )
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

            'Expression [conditional].[comparison].[comparison].[function].[native-function]' .
            ' - parameter textString would get [number], expects [text]. :: ' .
            'Expression [conditional].[comparison]' .
            ' - operands "boolean" and "text" do not both match just one of the provided allowed result types, ' .
            'allowed types are: "boolean", "number", "text". :: ' .
            'Expression [conditional].[concatenation].[list].[expression-list].[binary-arithmetic]' .
            ' - right operand would get [text], expects [number]. :: ' .
            'Expression [conditional].[concatenation]' .
            ' - operand list would get [list<boolean|number|text|list<text>|text>], ' .
            'expects [list<text|number>]'
        );

        $this->expressionValidator->validate($expressionNode, $this->environmentNode)->throwIfViolated();
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
        $this->expressionValidator->validate($expressionNode, $this->environmentNode)->throwIfViolated();
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

            'Expression [binary-arithmetic] - Division by zero'
        );

        // Should throw, as divisor can statically be read as const(0)
        $this->expressionValidator->validate($expressionNode, $this->environmentNode)->throwIfViolated();
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
        $this->expressionValidator->validate($expressionNode, $this->environmentNode)->throwIfViolated();
    }
}
