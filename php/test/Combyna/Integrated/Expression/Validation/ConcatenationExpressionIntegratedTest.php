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
use Combyna\Component\Bag\Config\Act\ExpressionListNode;
use Combyna\Component\Environment\Config\Act\EnvironmentNode;
use Combyna\Component\Expression\Config\Act\BooleanExpressionNode;
use Combyna\Component\Expression\Config\Act\ConcatenationExpressionNode;
use Combyna\Component\Expression\Config\Act\ListExpressionNode;
use Combyna\Component\Expression\Config\Act\NothingExpressionNode;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Program\Validation\Validator\NodeValidator;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Exception\ValidationFailureException;
use Combyna\Harness\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ConcatenationExpressionIntegratedTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpressionIntegratedTest extends TestCase
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

    public function testResultTypeIsText()
    {
        $expressionNode = new ConcatenationExpressionNode(
            new ListExpressionNode(
                new ExpressionListNode([
                    new TextExpressionNode('The result is: '),
                    new NumberExpressionNode(101)
                ])
            )
        );
        $rootValidationContext = $this->nodeValidator->validate($expressionNode, $this->appNode);

        $type = $rootValidationContext->getExpressionResultType($expressionNode);

        $this->assert($type)->isAnInstanceOf(StaticType::class);
        $this->assert($type->getSummary())->exactlyEquals('text');
    }

    public function testTheOperandListExpressionCanOnlyEvaluateToAListOfNumbersOrTexts()
    {
        $expressionNode = new ConcatenationExpressionNode(
            new ListExpressionNode(
                new ExpressionListNode([
                    new BooleanExpressionNode(true),
                    new NothingExpressionNode()
                ])
            )
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[concatenation]' .
            ' - operand list would get [list<boolean|nothing>], expects [list<text|number>]'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }

    public function testTheOperandListExpressionMustBeValid()
    {
        $expressionNode = new ConcatenationExpressionNode(
            new UnknownExpressionNode('my-unknown-expr-type')
        );

        $this->setExpectedException(
            ValidationFailureException::class,

            'ACT node [detached].[concatenation]' .
            ' - operand list would get [unknown<Expression type "my-unknown-expr-type">], expects [list<text|number>]'
        );

        $this->nodeValidator->validate($expressionNode, $this->appNode)->throwIfViolated();
    }
}
