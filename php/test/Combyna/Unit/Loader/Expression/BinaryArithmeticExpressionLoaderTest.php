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

use Combyna\Component\Expression\BinaryArithmeticExpression;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Expression\Config\Loader\BinaryArithmeticExpressionLoader;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class BinaryArithmeticExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BinaryArithmeticExpressionLoaderTest extends TestCase
{
    /**
     * @var ObjectProphecy|BinaryArithmeticExpression
     */
    private $binaryExpression;

    /**
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

    /**
     * @var ObjectProphecy|ExpressionFactoryInterface
     */
    private $expressionFactory;

    /**
     * @var ObjectProphecy|ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @var BinaryArithmeticExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->binaryExpression = $this->prophesize(BinaryArithmeticExpression::class);
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->expressionFactory = $this->prophesize(ExpressionFactoryInterface::class);
        $this->expressionLoader = $this->prophesize(ExpressionLoaderInterface::class);

        $this->configParser->getElement(Argument::any(), Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[0][$args[1]];
            });

        $this->loader = new BinaryArithmeticExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionFactory->reveal(),
            $this->expressionLoader->reveal()
        );
    }

    public function testLoadReturnsACorrectlyBuiltBinaryArithmeticExpression()
    {
        $config = [
            'type' => 'binary-arithmetic',
            'left' => [
                'type' => 'number',
                'number' => 21
            ],
            'operator' => '+',
            'right' => [
                'type' => 'number',
                'number' => 101.99
            ]
        ];
        $leftOperandExpression = $this->prophesize(ExpressionInterface::class);
        $rightOperandExpression = $this->prophesize(ExpressionInterface::class);
        $this->expressionLoader->load($config['left'])->willReturn($leftOperandExpression->reveal());
        $this->expressionLoader->load($config['right'])->willReturn($rightOperandExpression->reveal());
        $this->expressionFactory->createBinaryArithmeticExpression(
            Argument::is($leftOperandExpression->reveal()),
            '+',
            Argument::is($rightOperandExpression->reveal())
        )->willReturn($this->binaryExpression->reveal());

        $resultExpression = $this->loader->load($config);

        $this->assert($resultExpression)->exactlyEquals($this->binaryExpression->reveal());

    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('binary-arithmetic');
    }
}
