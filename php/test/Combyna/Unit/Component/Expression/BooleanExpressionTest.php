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
use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
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

    public function setUp()
    {
        $this->evaluationContext = $this->prophesize(EvaluationContextInterface::class);

        $this->expression = new BooleanExpression(true);
    }

    /**
     * @dataProvider booleanProvider
     * @param bool $boolean
     */
    public function testConstructorAllowsValidBooleans($boolean)
    {
        // No exception expected.
        $this->expectNotToPerformAssertions();

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
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
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

    public function testGetTypeReturnsTheBooleanType()
    {
        static::assertSame('boolean', $this->expression->getType());
    }

    public function testToNativeReturnsTheNativeBooleanValueWhenTrue()
    {
        static::assertTrue((new BooleanExpression(true))->toNative());
    }

    public function testToNativeReturnsTheNativeBooleanValueWhenFalse()
    {
        static::assertFalse((new BooleanExpression(false))->toNative());
    }

    public function testToStaticReturnsItself()
    {
        static::assertSame($this->expression, $this->expression->toStatic($this->evaluationContext->reveal()));
    }
}
