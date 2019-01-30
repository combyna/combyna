<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Assurance;

use Combyna\Component\Bag\MutableStaticBagInterface;
use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\Assurance\NonZeroNumberAssurance;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\TextExpression;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Argument;
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
     * @var ObjectProphecy|MutableStaticBagInterface
     */
    private $staticBag;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->inputExpression = $this->prophesize(ExpressionInterface::class);
        $this->resultStatic = $this->prophesize(NumberExpression::class);
        $this->staticBag = $this->prophesize(MutableStaticBagInterface::class);

        $this->inputExpression->toStatic(Argument::is($this->evaluationContext->reveal()))
            ->willReturn($this->resultStatic->reveal());
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
        $this->assert($this->assurance->getConstraint())->exactlyEquals(NonZeroNumberAssurance::TYPE);
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
}
