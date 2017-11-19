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

use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ComparisonExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var ComparisonExpression
     */
    private $expression;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $leftOperandExpression;

    /**
     * @var ObjectProphecy|ExpressionInterface
     */
    private $rightOperandExpression;

    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $subEvaluationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);
        $this->leftOperandExpression = $this->prophesize(ExpressionInterface::class);
        $this->rightOperandExpression = $this->prophesize(ExpressionInterface::class);
        $this->subEvaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $createBooleanExpression = function (array $args) {
            $numberExpression = $this->prophesize(BooleanExpression::class);
            $numberExpression->toNative()->willReturn($args[0]);

            return $numberExpression;
        };
        $this->expressionFactory->createBooleanExpression(Argument::any())->will(
            function (array $args) use ($createBooleanExpression) {
                return $createBooleanExpression($args);
            }
        );
    }

    public function testGetType()
    {
        $this->createExpression(ComparisonExpression::EQUAL_CASE_INSENSITIVE);

        $this->assert($this->expression->getType())->exactlyEquals('comparison');
    }

    public function testToStaticMatchesTwoTextsOfDifferentCaseCaseInsensitively()
    {
        $this->createExpressionWithOperands('world', ComparisonExpression::EQUAL_CASE_INSENSITIVE, 'WORld');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isTrue;
    }

    public function testToStaticDoesNotMatchTwoDifferentTextsCaseInsensitively()
    {
        $this->createExpressionWithOperands('world', ComparisonExpression::EQUAL_CASE_INSENSITIVE, 'not it');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isFalse;
    }

    public function testToStaticMatchesTwoIdenticalTextsOfSameCaseCaseSensitively()
    {
        $this->createExpressionWithOperands('thing', ComparisonExpression::EQUAL, 'thing');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isTrue;
    }

    public function testToStaticDoesNotMatchTwoSameTextsOfDifferentCaseCaseSensitively()
    {
        $this->createExpressionWithOperands('thing', ComparisonExpression::EQUAL, 'THIng');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isFalse;
    }

    /**
     * @param int|float $leftOperandNative
     * @param string $operator
     * @param int|float $rightOperandNative
     */
    private function createExpressionWithOperands($leftOperandNative, $operator, $rightOperandNative)
    {
        $leftOperandStatic = $this->prophesize(NumberExpression::class);
        $leftOperandStatic->toNative()->willReturn($leftOperandNative);
        $this->leftOperandExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($leftOperandStatic->reveal());
        $rightOperandStatic = $this->prophesize(NumberExpression::class);
        $rightOperandStatic->toNative()->willReturn($rightOperandNative);
        $this->rightOperandExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($rightOperandStatic->reveal());

        $this->createExpression($operator);
    }

    /**
     * @param string $operator
     */
    private function createExpression($operator)
    {
        $this->expression = new ComparisonExpression(
            $this->expressionFactory->reveal(),
            $this->leftOperandExpression->reveal(),
            $operator,
            $this->rightOperandExpression->reveal()
        );

        $this->evaluationContext->createSubExpressionContext(Argument::is($this->expression))
            ->willReturn($this->subEvaluationContext->reveal());
    }
}
