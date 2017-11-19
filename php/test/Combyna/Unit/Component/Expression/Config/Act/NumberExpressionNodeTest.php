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

use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Type\StaticType;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NumberExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpressionNodeTest extends TestCase
{
    /**
     * @var NumberExpressionNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->node = new NumberExpressionNode(21);
    }

    /**
     * @dataProvider numberProvider
     * @param number $number
     */
    public function testConstructorAllowsValidNumbers($number)
    {
        new NumberExpressionNode($number);
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
            'NumberExpressionNode expects a float or int, ' . $type . ' given'
        );

        new NumberExpressionNode($nonNumber);
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

    public function testGetResultTypeReturnsAStaticNumberType()
    {
        $resultType = $this->node->getResultType($this->validationContext->reveal());

        $this->assert($resultType)->isAnInstanceOf(StaticType::class);
        $this->assert($resultType->getSummary())->exactlyEquals('number');
    }

    public function testGetTypeReturnsTheNumberType()
    {
        $this->assert($this->node->getType())->exactlyEquals('number');
    }

    public function testToNativeReturnsTheNativeNumberValue()
    {
        $this->assert($this->node->toNative())->exactlyEquals(21);
    }
}
