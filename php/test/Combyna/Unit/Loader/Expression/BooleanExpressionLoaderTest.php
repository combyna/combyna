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

use Combyna\Component\Expression\BooleanExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Expression\Config\Loader\BooleanExpressionLoader;
use Combyna\Component\Config\Loader\ConfigParser;
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
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var BooleanExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);

        $this->expressionFactory->createBooleanExpression(Argument::any())
            ->will($this->noBind(function (array $args) {
                /** @var ObjectProphecy|BooleanExpression $booleanExpression */
                $booleanExpression = $this->prophesize(BooleanExpression::class);
                $booleanExpression->toNative()->willReturn($args[0]);

                return $booleanExpression;
            }));

        $this->loader = new BooleanExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionFactory->reveal()
        );
    }

    /**
     * @dataProvider booleanProvider
     * @param boolean $boolean
     */
    public function testLoadReturnsABooleanExpressionWithTheCorrectNativeValue($boolean)
    {
        $config = [
            'type' => 'boolean',
            'boolean' => $boolean
        ];
        $this->configParser->getElement($config, 'boolean', Argument::any())
            ->willReturn($boolean);

        $booleanExpression = $this->loader->load($config);

        $this->assert($booleanExpression)->isAnInstanceOf(BooleanExpression::class);
        $this->assert($booleanExpression->toNative())->exactlyEquals($boolean);
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
        $this->assert($this->loader->getType())->exactlyEquals('boolean');
    }
}
