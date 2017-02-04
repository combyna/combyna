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
use Combyna\Expression\NumberExpression;
use Combyna\Expression\Validation\ValidationContextInterface;
use Combyna\Harness\TestCase;
use Combyna\Type\StaticType;
use InvalidArgumentException;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NumberExpressionTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpressionTest extends TestCase
{
    /**
     * @var ObjectProphecy|EvaluationContextInterface
     */
    private $evaluationContext;

    /**
     * @var NumberExpression
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

        $this->expression = new NumberExpression(21);
    }

    /**
     * @dataProvider numberProviderProvider
     * @param number $number
     */
    public function testConstructorAllowsValidNumbers($number)
    {
        new NumberExpression($number);
    }

    /**
     * @return array
     */
    public function numberProviderProvider()
    {
        return [
            'integer' => [21],
            'float' => [27.2]
        ];
    }

    /**
     * @dataProvider nonNumberProviderWithTypeProvider
     * @param mixed $nonNumber
     * @param string $type
     */
    public function testConstructorThrowsExceptionWhenNonNumberGiven($nonNumber, $type)
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'NumberExpression expects a float or int, ' . $type . ' given'
        );

        new NumberExpression($nonNumber);
    }

    /**
     * @return array
     */
    public function nonNumberProviderWithTypeProvider()
    {
        return [
            'string' => ['hello', 'string'],
            'null' => [null, 'NULL'],
            'boolean' => [false, 'boolean']
        ];
    }

    public function testGetResultTypeReturnsAStaticNumberType()
    {
        $resultType = $this->expression->getResultType($this->validationContext->reveal());

        $this->assert($resultType)->isAnInstanceOf(StaticType::class);
        $this->assert($resultType->getSummary())->exactlyEquals('number');
    }

    public function testGetTypeReturnsTheNumberType()
    {
        $this->assert($this->expression->getType())->exactlyEquals('number');
    }

    public function testToNativeReturnsTheNativeNumberValue()
    {
        $this->assert($this->expression->toNative())->exactlyEquals(21);
    }

    public function testToStaticReturnsItself()
    {
        $this->assert($this->expression->toStatic($this->evaluationContext->reveal()))
            ->exactlyEquals($this->expression);
    }
}
