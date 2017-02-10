<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Expression;

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\ConcatenationExpression;
use Combyna\Expression\ExpressionFactoryInterface;
use Combyna\Expression\ExpressionInterface;
use Combyna\Expression\NumberExpression;
use Combyna\Expression\StaticListExpression;
use Combyna\Expression\TextExpression;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Type\StaticListType;
use Combyna\Type\StaticType;
use LogicException;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ConcatenationExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var ConcatenationExpression
     */
    private $expression;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $operandListExpression;

    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $subEvaluationContext;

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
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);
        $this->operandListExpression = $this->prophesize(ExpressionInterface::class);
        $this->subEvaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->subValidationContext = $this->prophesize(ValidationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->operandListExpression->validate(Argument::is($this->subValidationContext->reveal()))
            ->willReturn(null);

        $this->expression = new ConcatenationExpression(
            $this->expressionFactory->reveal(),
            $this->operandListExpression->reveal()
        );

        $this->evaluationContext->createSubContext(Argument::is($this->expression))
            ->willReturn($this->subEvaluationContext->reveal());
        $this->validationContext->createSubContext(Argument::is($this->expression))
            ->willReturn($this->subValidationContext->reveal());
    }

    public function testGetResultTypeReturnsAStaticTextType()
    {
        $resultType = $this->expression->getResultType($this->validationContext->reveal());

        $this->assert($resultType)->isAnInstanceOf(StaticType::class);
        $this->assert($resultType->getSummary())->exactlyEquals('text');
    }

    public function testGetType()
    {
        $this->assert($this->expression->getType())->exactlyEquals('concatenation');
    }

    public function testToStaticReturnsTheConcatenatedTextExpressionFromTheList()
    {
        /** @var ObjectProphecy|TextExpression $concatenatedText */
        $concatenatedText = $this->prophesize(TextExpression::class);
        $concatenatedText->toNative()->willReturn('my concatenated text');
        /** @var ObjectProphecy|StaticListExpression $operandListStatic */
        $operandListStatic = $this->prophesize(StaticListExpression::class);
        $operandListStatic->concatenate()->willReturn($concatenatedText);
        $this->operandListExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($operandListStatic->reveal());

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(TextExpression::class);
        $this->assert($resultStatic->toNative())->exactlyEquals('my concatenated text');
    }

    public function testToStaticThrowsLogicExceptionWhenListExpressionEvaluatesToANonStaticListExpression()
    {
        /** @var ObjectProphecy|NumberExpression $operandListStatic */
        $operandListStatic = $this->prophesize(NumberExpression::class); // A non-static list
        $operandListStatic->getType()->willReturn('number');
        $this->operandListExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($operandListStatic->reveal());

        $this->setExpectedException(
            LogicException::class,
            'ConcatenationExpression :: List can only evaluate to a static-list ' .
            'or error expression, but got a(n) "number"'
        );

        $this->expression->toStatic($this->evaluationContext->reveal());
    }

    public function testValidateValidatesTheOperandListExpressionInASubValidationContext()
    {
        $this->expression->validate($this->validationContext->reveal());

        $this->operandListExpression->validate(Argument::is($this->subValidationContext))
            ->shouldHaveBeenCalled();
    }

    public function testValidateChecksTheOperandListExpressionCanOnlyEvaluateToAListOfNumbersOrTexts()
    {
        $this->expression->validate($this->validationContext->reveal());

        $this->subValidationContext->assertResultType(
            Argument::is($this->operandListExpression->reveal()),
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
