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

use Combyna\Bag\StaticBagInterface;
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
     * @var ObjectProphecy|StaticBagInterface
     */
    private $staticBag;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->inputExpression = $this->prophesize(ExpressionInterface::class);
        $this->resultStatic = $this->prophesize(NumberExpression::class);
        $this->staticBag = $this->prophesize(StaticBagInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->inputExpression->toStatic(Argument::is($this->evaluationContext->reveal()))
            ->willReturn($this->resultStatic->reveal());
        $this->inputExpression->validate(Argument::is($this->validationContext->reveal()))
            ->willReturn(null);
        $this->resultStatic->toNative()->willReturn(2);

        $this->assurance = new NonZeroNumberAssurance($this->inputExpression->reveal(), 'my-static');
    }

    public function testDefinesStaticReturnsTrueForTheSpecifiedName()
    {
        $this->assert($this->assurance->definesStatic('my-static'))->isTrue;
    }

    public function testDefinesStaticReturnsFalseForAnotherName()
    {
        $this->assert($this->assurance->definesStatic('not-my-static'))->isFalse;
    }

    public function testGetConstraintReturnsCorrectValue()
    {
        $this->assert($this->assurance->getConstraint())->exactlyEquals(AssuranceInterface::NON_ZERO_NUMBER);
    }

    public function testGetTypeReturnsTheResultTypeOfTheExpressionWhenGivenTheCorrectStaticName()
    {
        $type = $this->prophesize(TypeInterface::class);
        $this->inputExpression->getResultType(Argument::is($this->validationContext->reveal()))
            ->willReturn($type);

        $this->assert($this->assurance->getStaticType($this->validationContext->reveal(), 'my-static'))
            ->exactlyEquals($type->reveal());
    }

    public function testGetTypeThrowsExceptionWhenGivenTheWrongStaticName()
    {
        $this->setExpectedException(
            LogicException::class,
            'NonZeroNumberAssurance only defines static "my-static" but was asked about "not-my-static"'
        );

        $this->assurance->getStaticType($this->validationContext->reveal(), 'not-my-static');
    }

    public function testEvaluateReturnsTrueWhenTheExpressionEvaluatesToANonZeroNumber()
    {
        $this->assert(
            $this->assurance->evaluate($this->evaluationContext->reveal(), $this->staticBag->reveal())
        )->isTrue;
    }

    public function testEvaluateStoresTheStaticInTheBagWhenItEvaluatesToANonZeroNumber()
    {
        $this->assurance->evaluate($this->evaluationContext->reveal(), $this->staticBag->reveal());

        $this->staticBag->setStatic('my-static', Argument::is($this->resultStatic->reveal()))->shouldHaveBeenCalled();
    }

    public function testEvaluateReturnsFalseWhenTheExpressionEvaluatesToZero()
    {
        $this->resultStatic->toNative()->willReturn(0);

        $this->assert(
            $this->assurance->evaluate($this->evaluationContext->reveal(), $this->staticBag->reveal())
        )->isFalse;
    }

    public function testEvaluateDoesNotStoreAnyStaticInTheBagWhenTheExpressionEvaluatesToZero()
    {
        $this->resultStatic->toNative()->willReturn(0);

        $this->assurance->evaluate($this->evaluationContext->reveal(), $this->staticBag->reveal());

        $this->staticBag->setStatic('my-static', Argument::any())->shouldNotHaveBeenCalled();
    }

    public function testEvaluateThrowsExceptionWhenExpressionEvaluatesToANonNumber()
    {
        $textStatic = $this->prophesize(TextExpression::class);
        $textStatic->getType()->willReturn('text');
        $this->inputExpression->toStatic(Argument::is($this->evaluationContext->reveal()))
            ->willReturn($textStatic->reveal());

        $this->setExpectedException(
            LogicException::class,
            'NonZeroNumberAssurance should receive a number, but got "text"'
        );

        $this->assurance->evaluate($this->evaluationContext->reveal(), $this->staticBag->reveal());
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
