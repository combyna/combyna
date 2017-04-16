<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\Component\Expression\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\NumberExpressionNode;
use Combyna\Component\Expression\Config\Loader\NumberExpressionLoader;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class NumberExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberExpressionLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var NumberExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);

        $this->loader = new NumberExpressionLoader($this->configParser->reveal());
    }

    /**
     * @dataProvider numberProvider
     * @param number $number
     */
    public function testLoadReturnsANumberExpressionNodeWithTheCorrectNativeValue($number)
    {
        $config = [
            'type' => 'number',
            'number' => $number
        ];
        $this->configParser->getElement($config, 'number', Argument::any())
            ->willReturn($number);

        $numberExpression = $this->loader->load($config);

        $this->assert($numberExpression)->isAnInstanceOf(NumberExpressionNode::class);
        $this->assert($numberExpression->toNative())->exactlyEquals($number);
    }

    /**
     * @return array
     */
    public function numberProvider()
    {
        return [
            [21],
            [27.2]
        ];
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('number');
    }
}
