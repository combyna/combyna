<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Expression\Assurance;

use Combyna\Evaluation\EvaluationContextInterface;
use Combyna\Expression\Assurance\AssuranceInterface;
use Combyna\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Expression\ExpressionInterface;
use Combyna\Expression\NumberExpression;
use Combyna\Expression\TextExpression;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Type\StaticType;
use Combyna\Type\TypeInterface;
use LogicException;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NonZeroNumberAssuranceTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroNumberAssuranceTest extends TestCase
{
    /**
     * @var NonZeroNumberAssurance
     */
    private $assurance;

    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $inputExpression;

    /**
     * @var ObjectProphecy|NumberExpression
     */
    private $resultStatic;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->inputExpression = $this->prophesize(ExpressionInterface::class);
        $this->resultStatic = $this->prophesize(NumberExpression::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->inputExpression->toStatic(Argument::is($this->evaluationContext->reveal()))
            ->willReturn($this->resultStatic->reveal());
        $this->inputExpression->validate(Argument::is($this->validationContext->reveal()))
            ->willReturn(null);
        $this->resultStatic->toNative()->willReturn(2);

        $this->assurance = new NonZeroNumberAssurance($this->inputExpression->reveal(), 'my-static');
    }

    public function testGetConstraintReturnsCorrectValue()
    {
        $this->assert($this->assurance->getConstraint())->exactlyEquals(AssuranceInterface::NON_ZERO_NUMBER);
    }

    public function testGetNameReturnsTheNameOfTheAssuredStatic()
    {
        $this->assert($this->assurance->getName())->exactlyEquals('my-static');
    }

    public function testGetTypeReturnsTheResultTypeOfTheExpression()
    {
        $type = $this->prophesize(TypeInterface::class);
        $this->inputExpression->getResultType(Argument::is($this->validationContext->reveal()))
            ->willReturn($type);

        $this->assert($this->assurance->getType($this->validationContext->reveal()))
            ->exactlyEquals($type->reveal());
    }

    public function testToStaticReturnsTheStaticWhenItEvaluatesToANonZeroNumber()
    {
        $this->assert(
            $this->assurance->toStatic($this->evaluationContext->reveal())
        )->exactlyEquals($this->resultStatic->reveal());
    }

    public function testToStaticReturnsNullWhenItEvaluatesToZero()
    {
        $this->resultStatic->toNative()->willReturn(0);

        $this->assert(
            $this->assurance->toStatic($this->evaluationContext->reveal())
        )->isNull;
    }

    public function testToStaticThrowsExceptionWhenExpressionEvaluatesToANonNumber()
    {
        $textStatic = $this->prophesize(TextExpression::class);
        $textStatic->getType()->willReturn('text');
        $this->inputExpression->toStatic(Argument::is($this->evaluationContext->reveal()))
            ->willReturn($textStatic->reveal());

        $this->setExpectedException(
            LogicException::class,
            'NonZeroNumberAssurance should receive a number, but got "text"'
        );

        $this->assurance->toStatic($this->evaluationContext->reveal());
    }

    public function testValidateValidatesTheExpression()
    {
        $this->assurance->validate($this->validationContext->reveal());

        $this->inputExpression->validate(Argument::is($this->validationContext->reveal()))
            ->shouldHaveBeenCalled();
    }

    public function testValidateAssertsThatTheExpressionCanOnlyEvaluateToANumber()
    {
        $this->assurance->validate($this->validationContext->reveal());

        $this->validationContext->assertResultType(
            Argument::is($this->inputExpression->reveal()),
            Argument::any(),
            'non-zero assurance'
        )
            ->shouldHaveBeenCalled()
            ->shouldHave($this->noBind(function (array $calls) {
                /** @var Call[] $calls */
                list(, $type) = $calls[0]->getArguments();
                /** @var StaticType $type */
                $this->assert($type)->isAnInstanceOf(StaticType::class);
                $this->assert($type->getSummary())->exactlyEquals('number');
            }));
    }
}
