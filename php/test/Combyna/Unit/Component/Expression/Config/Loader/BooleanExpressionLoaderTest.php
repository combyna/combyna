<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\BooleanExpressionNode;
use Combyna\Component\Expression\Config\Loader\BooleanExpressionLoader;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BooleanExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BooleanExpressionLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var BooleanExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);

        $this->loader = new BooleanExpressionLoader($this->configParser->reveal());
    }

    /**
     * @dataProvider booleanProvider
     * @param boolean $boolean
     */
    public function testLoadReturnsABooleanExpressionNodeWithTheCorrectNativeValue($boolean)
    {
        $config = [
            'type' => 'boolean',
            'boolean' => $boolean
        ];
        $this->configParser->getElement($config, 'boolean', Argument::any(), 'boolean')
            ->willReturn($boolean);

        $booleanExpressionNode = $this->loader->load($config);

        static::assertInstanceOf(BooleanExpressionNode::class, $booleanExpressionNode);
        static::assertSame($boolean, $booleanExpressionNode->toNative());
    }

    /**
     * @return array
     */
    public function booleanProvider()
    {
        return [
            [true],
            [false]
        ];
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        static::assertSame('boolean', $this->loader->getType());
    }
}
