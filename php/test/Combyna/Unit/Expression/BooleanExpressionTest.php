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
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Type\StaticType;
use InvalidArgumentException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BooleanExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var BooleanExpression
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

        $this->expression = new BooleanExpression(true);
    }

    /**
     * @dataProvider booleanProvider
     * @param bool $boolean
     */
    public function testConstructorAllowsValidBooleans($boolean)
    {
        new BooleanExpression($boolean);
    }

    /**
     * @return array
     */
    public function booleanProvider()
    {
        return [
            'true' => [true],
            'false' => [false]
        ];
    }

    /**
     * @dataProvider nonBooleanWithTypeProvider
     * @param mixed $nonBoolean
     * @param string $type
     */
    public function testConstructorThrowsExceptionWhenNonBooleanGiven($nonBoolean, $type)
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'BooleanExpression expects a boolean, ' . $type . ' given'
        );

        new BooleanExpression($nonBoolean);
    }

    /**
     * @return array
     */
    public function nonBooleanWithTypeProvider()
    {
        return [
            'string' => ['hello', 'string'],
            'null' => [null, 'NULL'],
            'int' => [21, 'integer'],
            'float' => [27.7, 'double']
        ];
    }

    public function testGetResultTypeReturnsAStaticBooleanType()
    {
        $resultType = $this->expression->getResultType($this->validationContext->reveal());

        $this->assert($resultType)->isAnInstanceOf(StaticType::class);
        $this->assert($resultType->getSummary())->exactlyEquals('boolean');
    }

    public function testGetTypeReturnsTheBooleanType()
    {
        $this->assert($this->expression->getType())->exactlyEquals('boolean');
    }

    public function testToNativeReturnsTheNativeBooleanValueWhenTrue()
    {
        $this->assert((new BooleanExpression(true))->toNative())->isTrue;
    }

    public function testToNativeReturnsTheNativeBooleanValueWhenFalse()
    {
        $this->assert((new BooleanExpression(false))->toNative())->isFalse;
    }

    public function testToStaticReturnsItself()
    {
        $this->assert($this->expression->toStatic($this->evaluationContext->reveal()))
            ->exactlyEquals($this->expression);
    }
}
