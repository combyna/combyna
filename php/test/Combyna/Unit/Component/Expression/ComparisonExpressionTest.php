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
use Combyna\Component\Expression\TextExpression;
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

        static::assertSame('comparison', $this->expression->getType());
    }

    public function testToStaticMatchesTwoEqualIntegers()
    {
        $this->createExpressionWithNumberOperands(21, ComparisonExpression::EQUAL, 21);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertTrue($resultStatic->toNative());
    }

    public function testToStaticDoesNotMatchTwoUnequalIntegers()
    {
        $this->createExpressionWithNumberOperands(1001, ComparisonExpression::EQUAL, 21);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertFalse($resultStatic->toNative());
    }

    public function testToStaticMatchesTwoEqualFloats()
    {
        $this->createExpressionWithNumberOperands(123.456, ComparisonExpression::EQUAL, 123.456);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertTrue($resultStatic->toNative());
    }

    public function testToStaticDoesNotMatchTwoUnequalFloats()
    {
        $this->createExpressionWithNumberOperands(654.456, ComparisonExpression::EQUAL, 123.456);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertFalse($resultStatic->toNative());
    }

    public function testToStaticMatchesAnIntegerAndEqualFloat()
    {
        $this->createExpressionWithNumberOperands(123, ComparisonExpression::EQUAL, 123.0);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertTrue($resultStatic->toNative());
    }

    public function testToStaticDoesNotMatchAnIntegerAndUnequalFloat()
    {
        $this->createExpressionWithNumberOperands(123, ComparisonExpression::EQUAL, 123.4);

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertFalse($resultStatic->toNative());
    }

    public function testToStaticMatchesTwoTextsOfDifferentCaseCaseInsensitively()
    {
        $this->createExpressionWithTextOperands('world', ComparisonExpression::EQUAL_CASE_INSENSITIVE, 'WORld');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertTrue($resultStatic->toNative());
    }

    public function testToStaticDoesNotMatchTwoDifferentTextsCaseInsensitively()
    {
        $this->createExpressionWithTextOperands('world', ComparisonExpression::EQUAL_CASE_INSENSITIVE, 'not it');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertFalse($resultStatic->toNative());
    }

    public function testToStaticMatchesTwoIdenticalTextsOfSameCaseCaseSensitively()
    {
        $this->createExpressionWithTextOperands('thing', ComparisonExpression::EQUAL, 'thing');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertTrue($resultStatic->toNative());
    }

    public function testToStaticDoesNotMatchTwoSameTextsOfDifferentCaseCaseSensitively()
    {
        $this->createExpressionWithTextOperands('thing', ComparisonExpression::EQUAL, 'THIng');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        static::assertInstanceOf(BooleanExpression::class, $resultStatic);
        static::assertFalse($resultStatic->toNative());
    }

    /**
     * @param int|float $leftOperandNative
     * @param string $operator
     * @param int|float $rightOperandNative
     */
    private function createExpressionWithNumberOperands($leftOperandNative, $operator, $rightOperandNative)
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
     * @param string $leftOperandNative
     * @param string $operator
     * @param string $rightOperandNative
     */
    private function createExpressionWithTextOperands($leftOperandNative, $operator, $rightOperandNative)
    {
        $leftOperandStatic = $this->prophesize(TextExpression::class);
        $leftOperandStatic->toNative()->willReturn($leftOperandNative);
        $this->leftOperandExpression->toStatic(Argument::is($this->subEvaluationContext->reveal()))
            ->willReturn($leftOperandStatic->reveal());
        $rightOperandStatic = $this->prophesize(TextExpression::class);
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
