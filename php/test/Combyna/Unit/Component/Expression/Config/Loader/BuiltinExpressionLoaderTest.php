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
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\Config\Loader\BuiltinExpressionLoader;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BuiltinExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BuiltinExpressionLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var BuiltinExpressionLoader
     */
    private $loader;

    /**
     * @var ObjectProphecy|BuiltinLoaderInterface
     */
    private $subLoader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->subLoader = $this->prophesize(BuiltinLoaderInterface::class);

        $this->configParser->getElement(Argument::any(), Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[0][$args[1]];
            });
        $this->subLoader->getBuiltinName()->willReturn('my_builtin');

        $this->loader = new BuiltinExpressionLoader($this->configParser->reveal());
        $this->loader->addBuiltinLoader($this->subLoader->reveal());
    }

    public function testLoadReturnsTheResultFromAMatchingBuiltinLoader()
    {
        $config = [
            'type' => 'builtin',
            'name' => 'my_builtin',
            'positional-arguments' => [
                ['type' => 'text', 'text' => 'my_arg']
            ],
            'named-arguments' => []
        ];
        $expressionFromSubLoader = $this->prophesize(ExpressionInterface::class);
        $this->subLoader->load($config)->willReturn($expressionFromSubLoader->reveal());

        $resultExpression = $this->loader->load($config);

        static::assertSame($expressionFromSubLoader->reveal(), $resultExpression);
    }

    public function testLoadReturnsUnknownExpressionNodeWhenNoMatchingLoaderIsInstalled()
    {
        $resultExpression = $this->loader->load([
            'type' => 'builtin',
            'name' => 'invalid_builtin',
            'positional-arguments' => [],
            'named-arguments' => []
        ]);

        static::assertInstanceOf(UnknownExpressionNode::class, $resultExpression);
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        static::assertSame('builtin', $this->loader->getType());
    }
}
