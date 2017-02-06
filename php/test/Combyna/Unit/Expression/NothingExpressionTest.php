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
use Combyna\Expression\NothingExpression;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Type\StaticType;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NothingExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NothingExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var NothingExpression
     */
    private $expression;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->expression = new NothingExpression();
    }

    public function testGetResultTypeReturnsAStaticNothingType()
    {
        $resultType = $this->expression->getResultType($this->validationContext->reveal());

        $this->assert($resultType)->isAnInstanceOf(StaticType::class);
        $this->assert($resultType->getSummary())->exactlyEquals('nothing');
    }

    public function testGetTypeReturnsTheNothingType()
    {
        $this->assert($this->expression->getType())->exactlyEquals('nothing');
    }

    public function testToNativeReturnsTheNull()
    {
        $this->assert($this->expression->toNative())->isNull;
    }

    public function testToStaticReturnsItself()
    {
        $this->assert($this->expression->toStatic($this->evaluationContext->reveal()))
            ->exactlyEquals($this->expression);
    }
}
