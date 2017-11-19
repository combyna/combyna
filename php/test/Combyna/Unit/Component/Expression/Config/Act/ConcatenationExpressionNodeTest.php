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

use Combyna\Component\Expression\Config\Act\ConcatenationExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ConcatenationExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpressionNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $glueExpressionNode;

    /**
     * @var ConcatenationExpressionNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $operandListExpressionNode;

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
        $this->glueExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->operandListExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->subValidationContext = $this->prophesize(ValidationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->operandListExpressionNode->validate(Argument::is($this->subValidationContext->reveal()))
            ->willReturn(null);

        $this->node = new ConcatenationExpressionNode(
            $this->operandListExpressionNode->reveal(),
            $this->glueExpressionNode->reveal()
        );

        $this->validationContext->createSubActNodeContext(Argument::is($this->node))
            ->willReturn($this->subValidationContext->reveal());
    }

    public function testGetGlueExpressionFetchesTheExpressionWhenSet()
    {
        $this->assert($this->node->getGlueExpression())->isTheSameAs($this->glueExpressionNode->reveal());
    }

    public function testGetGlueExpressionReturnsNullWhenNotSet()
    {
        $node = new ConcatenationExpressionNode($this->operandListExpressionNode->reveal());

        $this->assert($node->getGlueExpression())->isNull;
    }

    public function testGetOperandListExpressionFetchesTheExpressionWhenSet()
    {
        $this->assert($this->node->getOperandListExpression())->isTheSameAs($this->operandListExpressionNode->reveal());
    }

    public function testGetResultTypeReturnsAStaticTextType()
    {
        $resultType = $this->node->getResultType($this->validationContext->reveal());

        $this->assert($resultType)->isAnInstanceOf(StaticType::class);
        $this->assert($resultType->getSummary())->exactlyEquals('text');
    }

    public function testGetType()
    {
        $this->assert($this->node->getType())->exactlyEquals('concatenation');
    }

    public function testValidateValidatesTheOperandListExpressionInASubValidationContext()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->operandListExpressionNode->validate(Argument::is($this->subValidationContext->reveal()))
            ->shouldHaveBeenCalled();
    }

    public function testValidateChecksTheOperandListExpressionCanOnlyEvaluateToAListOfNumbersOrTexts()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->subValidationContext->assertResultType(
            Argument::is($this->operandListExpressionNode->reveal()),
            Argument::any(),
            'operand list'
        )
            ->shouldHaveBeenCalled()
            ->shouldHave($this->noBind(function (array $calls) {
                /** @var Call[] $calls */
                list(, $type) = $calls[0]->getArguments();
                /** @var StaticType $type */
                $this->assert($type)->isAnInstanceOf(StaticListType::class);
                $this->assert($type->getSummary())->exactlyEquals('list<text|number>');
            }));
    }
}
