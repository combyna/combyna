<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Unit\ExpressionLanguage;

use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Expression\Config\Loader\BuiltinExpressionLoader;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use InvalidArgumentException;
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

        $this->assert($resultExpression)->exactlyEquals($expressionFromSubLoader->reveal());
    }

    public function testLoadThrowsExceptionWhenNoMatchingLoaderIsInstalled()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'No loader is registered for builtin "invalid_builtin"'
        );

        $this->loader->load([
            'type' => 'builtin',
            'name' => 'invalid_builtin',
            'positional-arguments' => [],
            'named-arguments' => []
        ]);
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('builtin');
    }
}
