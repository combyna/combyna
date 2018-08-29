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

use Combyna\Component\Expression\Config\Act\TextExpressionNode;
use Combyna\Component\Validator\Context\ValidationContextInterface;
use Combyna\Harness\TestCase;
use InvalidArgumentException;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class TextExpressionNodeTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextExpressionNodeTest extends TestCase
{
    /**
     * @var TextExpressionNode
     */
    private $node;

    /**
     * @var ObjectProphecy|ValidationContextInterface
     */
    private $validationContext;

    public function setUp()
    {
        $this->validationContext = $this->prophesize(ValidationContextInterface::class);

        $this->node = new TextExpressionNode('this is my string');
    }

    /**
     * @dataProvider stringProvider
     * @param string $string
     */
    public function testConstructorAllowsValidStrings($string)
    {
        new TextExpressionNode($string);
    }

    /**
     * @return array
     */
    public function stringProvider()
    {
        return [
            'non-empty' => ['this is my text'],
            'empty' => ['']
        ];
    }

    /**
     * @dataProvider nonStringWithTypeProvider
     * @param mixed $nonString
     * @param string $type
     */
    public function testConstructorThrowsExceptionWhenNonStringGiven($nonString, $type)
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'TextExpressionNode expects a string, ' . $type . ' given'
        );

        new TextExpressionNode($nonString);
    }

    /**
     * @return array
     */
    public function nonStringWithTypeProvider()
    {
        return [
            'int' => [21, 'integer'],
            'float' => [27.5, 'double'],
            'null' => [null, 'NULL'],
            'boolean' => [false, 'boolean']
        ];
    }

    public function testGetTypeReturnsTheTextType()
    {
        $this->assert($this->node->getType())->exactlyEquals('text');
    }

    public function testToNativeReturnsTheNativeTextString()
    {
        $this->assert($this->node->toNative())->exactlyEquals('this is my string');
    }
}
