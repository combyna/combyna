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

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ConcatenationExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\StaticListExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Type\StaticListType;
use Combyna\Component\Type\StaticType;
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

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);
        $this->operandListExpression = $this->prophesize(ExpressionInterface::class);
        $this->subEvaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $this->expression = new ConcatenationExpression(
            $this->expressionFactory->reveal(),
            $this->operandListExpression->reveal()
        );

        $this->evaluationContext->createSubExpressionContext(Argument::is($this->expression))
            ->willReturn($this->subEvaluationContext->reveal());
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
        $operandListStatic->concatenate('')->willReturn($concatenatedText);
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
}
