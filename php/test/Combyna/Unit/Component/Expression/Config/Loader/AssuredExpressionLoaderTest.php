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

use Combyna\Component\Config\Loader\ExpressionConfigParser;
use Combyna\Component\Expression\Config\Act\AssuredExpressionNode;
use Combyna\Component\Expression\Config\Loader\AssuredExpressionLoader;
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
     * @var ObjectProphecy|ExpressionConfigParser
     */
    private $configParser;

    /**
     * @var AssuredExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ExpressionConfigParser::class);

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

        $this->assert($assuredExpression)->isAnInstanceOf(AssuredExpressionNode::class);
        $this->assert($assuredExpression->getAssuredStaticName())->exactlyEquals('my-assured-static');
    }

    public function testGetBuiltinNameReturnsTheCorrectName()
    {
        $this->assert($this->loader->getBuiltinName())->exactlyEquals('assured');
    }
}
