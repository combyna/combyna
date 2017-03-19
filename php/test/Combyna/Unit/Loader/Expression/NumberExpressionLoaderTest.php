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

use Combyna\Component\Expression\NumberExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Expression\Config\Loader\NumberExpressionLoader;
use Combyna\Component\Config\Loader\ConfigParser;
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
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var NumberExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);

        $this->expressionFactory->createNumberExpression(Argument::any())
            ->will($this->noBind(function (array $args) {
                /** @var ObjectProphecy|NumberExpression $numberExpression */
                $numberExpression = $this->prophesize(NumberExpression::class);
                $numberExpression->toNative()->willReturn($args[0]);

                return $numberExpression;
            }));

        $this->loader = new NumberExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionFactory->reveal()
        );
    }

    /**
     * @dataProvider numberProvider
     * @param number $number
     */
    public function testLoadReturnsANumberExpressionWithTheCorrectNativeValue($number)
    {
        $config = [
            'type' => 'number',
            'number' => $number
        ];
        $this->configParser->getElement($config, 'number', Argument::any())
            ->willReturn($number);

        $numberExpression = $this->loader->load($config);

        $this->assert($numberExpression)->isAnInstanceOf(NumberExpression::class);
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
