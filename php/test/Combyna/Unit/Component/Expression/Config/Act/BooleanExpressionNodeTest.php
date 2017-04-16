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

use Combyna\Component\Expression\Config\Act\BooleanExpressionNode;
use Combyna\Component\Type\StaticType;
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
        $this->setExpectedException(
            InvalidArgumentException::class,
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
        $this->assert((new BooleanExpressionNode(true))->toNative())->isTrue;
    }

    public function testToNativeReturnsTheNativeBooleanValueWhenFalse()
    {
        $this->assert((new BooleanExpressionNode(false))->toNative())->isFalse;
    }
}
