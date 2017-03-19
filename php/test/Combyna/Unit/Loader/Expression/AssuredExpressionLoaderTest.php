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

use Combyna\Component\Expression\AssuredExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\TextExpression;
use Combyna\Harness\TestCase;
use Combyna\Component\Expression\Config\Loader\AssuredExpressionLoader;
use Combyna\Component\Config\Loader\ConfigParser;
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
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var AssuredExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);

        $this->expressionFactory->createAssuredExpression(Argument::any())
            ->will($this->noBind(function (array $args) {
                /** @var ObjectProphecy|AssuredExpression $assuredExpression */
                $assuredExpression = $this->prophesize(AssuredExpression::class);
                $assuredExpression->getAssuredStaticName()->willReturn($args[0]);

                return $assuredExpression;
            }));

        $this->loader = new AssuredExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionFactory->reveal()
        );
    }

    public function testLoadReturnsAnAssuredExpressionWithTheCorrectAssuredStaticName()
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

        $this->assert($assuredExpression)->isAnInstanceOf(AssuredExpression::class);
        $this->assert($assuredExpression->getAssuredStaticName())->exactlyEquals('my-assured-static');
    }

    public function testGetBuiltinNameReturnsTheCorrectName()
    {
        $this->assert($this->loader->getBuiltinName())->exactlyEquals('assured');
    }
}
