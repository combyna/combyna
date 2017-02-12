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
use Combyna\Expression\BooleanExpression;
use Combyna\Expression\ExpressionFactoryInterface;
use Combyna\Expression\ExpressionInterface;
use Combyna\Expression\NumberExpression;
use Combyna\Expression\TextComparisonExpression;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Type\StaticType;
use InvalidArgumentException;
use LogicException;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TextComparisonExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextComparisonExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var TextComparisonExpression
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
        $this->leftOperandExpression = $this->prophesize(ExpressionInterface::class);
        $this->rightOperandExpression = $this->prophesize(ExpressionInterface::class);
        $this->subEvaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->subValidationContext = $this->prophesize(ValidationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

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
        $this->leftOperandExpression->validate(Argument::is($this->subValidationContext->reveal()))
            ->willReturn(null);
        $this->rightOperandExpression->validate(Argument::is($this->subValidationContext->reveal()))
            ->willReturn(null);
    }

    public function testGetType()
    {
        $this->createExpression(TextComparisonExpression::EQUALS_CASE_SENSITIVE);

        $this->assert($this->expression->getType())->exactlyEquals('text-comparison');
    }

    public function testToStaticMatchesTwoTextsOfDifferentCaseCaseInsensitively()
    {
        $this->createExpressionWithOperands('world', TextComparisonExpression::EQUALS_CASE_INSENSITIVE, 'WORld');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isTrue;
    }

    public function testToStaticDoesNotMatchTwoDifferentTextsCaseInsensitively()
    {
        $this->createExpressionWithOperands('world', TextComparisonExpression::EQUALS_CASE_INSENSITIVE, 'not it');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isFalse;
    }

    public function testToStaticMatchesTwoIdenticalTextsOfSameCaseCaseSensitively()
    {
        $this->createExpressionWithOperands('thing', TextComparisonExpression::EQUALS_CASE_SENSITIVE, 'thing');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isTrue;
    }

    public function testToStaticDoesNotMatchTwoSameTextsOfDifferentCaseCaseSensitively()
    {
        $this->createExpressionWithOperands('thing', TextComparisonExpression::EQUALS_CASE_SENSITIVE, 'THIng');

        $resultStatic = $this->expression->toStatic($this->evaluationContext->reveal());

        $this->assert($resultStatic)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($resultStatic->toNative())->isFalse;
    }

    public function testGetResultTypeReturnsAStaticNumberType()
    {
        $this->createExpression(TextComparisonExpression::EQUALS_CASE_SENSITIVE);

        $type = $this->expression->getResultType($this->validationContext->reveal());

        $this->assert($type)->isAnInstanceOf(StaticType::class);
        $this->assert($type->getSummary())->exactlyEquals('boolean');
    }

    public function testValidateValidatesTheLeftOperandInASubValidationContext()
    {
        $this->createExpression(TextComparisonExpression::EQUALS_CASE_SENSITIVE);

        $this->expression->validate($this->validationContext->reveal());

        $this->leftOperandExpression->validate(Argument::is($this->subValidationContext))
            ->shouldHaveBeenCalled();
    }

    public function testValidateValidatesTheRightOperandInASubValidationContext()
    {
        $this->createExpression(TextComparisonExpression::EQUALS_CASE_SENSITIVE);

        $this->expression->validate($this->validationContext->reveal());

        $this->rightOperandExpression->validate(Argument::is($this->subValidationContext))
            ->shouldHaveBeenCalled();
    }

    public function testValidateChecksTheGivenOperatorIsValid()
    {
        $this->createExpression(TextComparisonExpression::EQUALS_CASE_SENSITIVE);

        $this->expression->validate($this->validationContext->reveal());

        $this->subValidationContext->assertOperator(
            TextComparisonExpression::EQUALS_CASE_SENSITIVE,
            [
                TextComparisonExpression::EQUALS_CASE_INSENSITIVE,
                TextComparisonExpression::EQUALS_CASE_SENSITIVE,
                TextComparisonExpression::IS_NOT_EQUAL_TO_CASE_INSENSITIVE,
                TextComparisonExpression::IS_NOT_EQUAL_TO_CASE_SENSITIVE
            ]
        )
            ->shouldHaveBeenCalled();
    }

    public function testValidateChecksTheLeftOperandCanOnlyEvaluateToAText()
    {
        $this->createExpression(TextComparisonExpression::EQUALS_CASE_SENSITIVE);

        $this->expression->validate($this->validationContext->reveal());

        $this->subValidationContext->assertResultType(
            Argument::is($this->leftOperandExpression->reveal()),
            Argument::any(),
            'left operand'
        )
            ->shouldHaveBeenCalled()
            ->shouldHave($this->noBind(function (array $calls) {
                /** @var Call[] $calls */
                list(, $type) = $calls[0]->getArguments();
                /** @var StaticType $type */
                $this->assert($type)->isAnInstanceOf(StaticType::class);
                $this->assert($type->getSummary())->exactlyEquals('text');
            }));
    }

    public function testValidateChecksTheRightOperandCanOnlyEvaluateToAText()
    {
        $this->createExpression(TextComparisonExpression::EQUALS_CASE_SENSITIVE);

        $this->expression->validate($this->validationContext->reveal());

        $this->subValidationContext->assertResultType(
            Argument::is($this->rightOperandExpression->reveal()),
            Argument::any(),
            'right operand'
        )
            ->shouldHaveBeenCalled()
            ->shouldHave($this->noBind(function (array $calls) {
                /** @var Call[] $calls */
                list(, $type) = $calls[0]->getArguments();
                /** @var StaticType $type */
                $this->assert($type)->isAnInstanceOf(StaticType::class);
                $this->assert($type->getSummary())->exactlyEquals('text');
            }));
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
        $this->expression = new TextComparisonExpression(
            $this->expressionFactory->reveal(),
            $this->leftOperandExpression->reveal(),
            $operator,
            $this->rightOperandExpression->reveal()
        );

        $this->evaluationContext->createSubContext(Argument::is($this->expression))
            ->willReturn($this->subEvaluationContext->reveal());
        $this->validationContext->createSubContext(Argument::is($this->expression))
            ->willReturn($this->subValidationContext->reveal());
    }
}
