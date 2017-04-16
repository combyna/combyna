<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression;

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Expression\NumberExpression;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
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

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $this->expression = new NumberExpression(21);
    }

    /**
     * @dataProvider numberProvider
     * @param number $number
     */
    public function testConstructorAllowsValidNumbers($number)
    {
        new NumberExpression($number);
    }

    /**
     * @return array
     */
    public function numberProvider()
    {
        return [
            'integer' => [21],
            'float' => [27.2]
        ];
    }

    /**
     * @dataProvider nonNumberWithTypeProvider
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
    public function nonNumberWithTypeProvider()
    {
        return [
            'string' => ['hello', 'string'],
            'null' => [null, 'NULL'],
            'boolean' => [false, 'boolean']
        ];
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
