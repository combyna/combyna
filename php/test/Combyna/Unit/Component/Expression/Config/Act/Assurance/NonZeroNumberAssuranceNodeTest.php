<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Config\Act\Assurance;

use Combyna\Component\Expression\Assurance\AssuranceInterface;
use Combyna\Component\Expression\Config\Act\Assurance\NonZeroNumberAssuranceNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Type\TypeInterface;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use LogicException;
use Prophecy\Argument;
use Prophecy\Call\Call;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NonZeroNumberAssuranceNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NonZeroNumberAssuranceNodeTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var ObjectProphecy|ExpressionNodeInterface
     */
    private $inputExpressionNode;

    /**
     * @var NonZeroNumberAssuranceNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->inputExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->inputExpressionNode->validate(Argument::is($this->validationContext->reveal()))
            ->willReturn(null);

        $this->node = new NonZeroNumberAssuranceNode($this->inputExpressionNode->reveal(), 'my-static');
    }

    public function testDefinesStaticReturnsTrueForTheSpecifiedName()
    {
        $this->assert($this->node->definesStatic('my-static'))->isTrue;
    }

    public function testDefinesStaticReturnsFalseForAnotherName()
    {
        $this->assert($this->node->definesStatic('not-my-static'))->isFalse;
    }

    public function testGetConstraintReturnsCorrectValue()
    {
        $this->assert($this->node->getConstraint())->exactlyEquals(AssuranceInterface::NON_ZERO_NUMBER);
    }

    public function testGetRequiredAssuredStaticNamesReturnsAnArrayWithJustTheStaticName()
    {
        $this->assert($this->node->getRequiredAssuredStaticNames())->equals(['my-static']);
    }

    public function testGetStaticTypeReturnsTheResultTypeOfTheExpressionWhenGivenTheCorrectStaticName()
    {
        $type = $this->prophesize(TypeInterface::class);
        $this->inputExpressionNode->getResultType(Argument::is($this->validationContext->reveal()))
            ->willReturn($type);

        $this->assert($this->node->getStaticType($this->validationContext->reveal(), 'my-static'))
            ->exactlyEquals($type->reveal());
    }

    public function testGetStaticTypeThrowsExceptionWhenGivenTheWrongStaticName()
    {
        $this->setExpectedException(
            LogicException::class,
            'NonZeroNumberAssurance only defines static "my-static" but was asked about "not-my-static"'
        );

        $this->node->getStaticType($this->validationContext->reveal(), 'not-my-static');
    }

    public function testValidateValidatesTheExpression()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->inputExpressionNode->validate(Argument::is($this->validationContext->reveal()))
            ->shouldHaveBeenCalled();
    }

    public function testValidateAssertsThatTheExpressionCanOnlyEvaluateToANumber()
    {
        $this->node->validate($this->validationContext->reveal());

        $this->validationContext->assertResultType(
            Argument::is($this->inputExpressionNode->reveal()),
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
