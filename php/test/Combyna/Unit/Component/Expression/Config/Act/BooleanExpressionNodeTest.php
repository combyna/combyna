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

use Combyna\Component\Expression\Config\Act\BooleanExpressionNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BooleanExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanExpressionNodeTest extends TestCase
{
    /**
     * @var BooleanExpressionNode
     */
    private $expression;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->expression = new BooleanExpressionNode(true);
    }

    /**
     * @dataProvider booleanProvider
     * @param bool $boolean
     */
    public function testConstructorAllowsValidBooleans($boolean)
    {
        $this->expectNotToPerformAssertions();

        new BooleanExpressionNode($boolean);
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
            'BooleanExpressionNode expects a boolean, ' . $type . ' given'
        );

        new BooleanExpressionNode($nonBoolean);
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
        static::assertTrue((new BooleanExpressionNode(true))->toNative());
    }

    public function testToNativeReturnsTheNativeBooleanValueWhenFalse()
    {
        static::assertFalse((new BooleanExpressionNode(false))->toNative());
    }
}
