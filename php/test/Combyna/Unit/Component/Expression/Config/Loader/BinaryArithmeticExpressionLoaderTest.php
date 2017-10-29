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
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Loader\BinaryArithmeticExpressionLoader;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Harness\TestCase;
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
     * @var ObjectProphecy|ConfigParser
     */
    private $configParser;

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
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->expressionLoader = $this->prophesize(ExpressionLoaderInterface::class);

        $this->configParser->getElement(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[0][$args[1]];
            });

        $this->loader = new BinaryArithmeticExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionLoader->reveal()
        );
    }

    public function testLoadReturnsACorrectlyBuiltBinaryArithmeticExpressionNode()
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
        $leftOperandExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $rightOperandExpressionNode = $this->prophesize(ExpressionNodeInterface::class);
        $this->expressionLoader->load($config['left'])->willReturn($leftOperandExpressionNode->reveal());
        $this->expressionLoader->load($config['right'])->willReturn($rightOperandExpressionNode->reveal());

        $resultExpressionNode = $this->loader->load($config);

        $this->assert($resultExpressionNode)->isAnInstanceOf(BinaryArithmeticExpressionNode::class);
        $this->assert($resultExpressionNode->getLeftOperandExpression())->isTheSameAs($leftOperandExpressionNode->reveal());
        $this->assert($resultExpressionNode->getOperator())->isTheSameAs('+');
        $this->assert($resultExpressionNode->getRightOperandExpression())->isTheSameAs($rightOperandExpressionNode->reveal());
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('binary-arithmetic');
    }
}
