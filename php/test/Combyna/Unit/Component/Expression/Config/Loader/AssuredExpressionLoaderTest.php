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

use Combyna\Component\Expression\Config\Act\AssuredExpressionNode;
use Combyna\Component\Expression\Config\Loader\AssuredExpressionLoader;
use Combyna\Component\Expression\Config\Loader\ExpressionConfigParserInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class AssuredExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AssuredExpressionLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ExpressionConfigParserInterface
     */
    private $configParser;

    /**
     * @var AssuredExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ExpressionConfigParserInterface::class);

        $this->loader = new AssuredExpressionLoader($this->configParser->reveal());
    }

    public function testLoadReturnsAnAssuredExpressionNodeWithTheCorrectAssuredStaticName()
    {
        $config = [
            'type' => 'assured',
            'positional-arguments' => [
                ['type' => 'text', 'text' => 'my-assured-static']
            ]
        ];
        $this->configParser->getPositionalArgumentNative($config, 0, TextExpression::TYPE, Argument::any())
            ->willReturn('my-assured-static');

        $assuredExpression = $this->loader->load($config);

        static::assertInstanceOf(AssuredExpressionNode::class, $assuredExpression);
        static::assertSame('my-assured-static', $assuredExpression->getAssuredStaticName());
    }

    public function testGetBuiltinNameReturnsTheCorrectName()
    {
        static::assertSame('assured', $this->loader->getBuiltinName());
    }
}
