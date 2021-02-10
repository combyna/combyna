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

use Combyna\Component\Config\Exception\ArgumentParseException;
use Combyna\Component\Config\Parameter\ArgumentBagInterface;
use Combyna\Component\Expression\Config\Act\BinaryArithmeticExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\UnknownExpressionNode;
use Combyna\Component\Expression\Config\Loader\BinaryArithmeticExpressionLoader;
use Combyna\Component\Expression\Config\Loader\ExpressionConfigParserInterface;
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
     * @var ObjectProphecy|ArgumentBagInterface
     */
    private $argumentBag;

    /**
     * @var ObjectProphecy|ExpressionConfigParserInterface
     */
    private $configParser;

    /**
     * @var BinaryArithmeticExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->argumentBag = $this->prophesize(ArgumentBagInterface::class);
        $this->configParser = $this->prophesize(ExpressionConfigParserInterface::class);

        $this->loader = new BinaryArithmeticExpressionLoader($this->configParser->reveal());
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
        $this->configParser->parseArguments($config, Argument::any())
            ->willReturn($this->argumentBag);
        $this->argumentBag->getNamedExpressionArgument('left')
            ->willReturn($leftOperandExpressionNode);
        $this->argumentBag->getNamedStringArgument('operator')
            ->willReturn('+');
        $this->argumentBag->getNamedExpressionArgument('right')
            ->willReturn($rightOperandExpressionNode);

        $resultExpressionNode = $this->loader->load($config);

        self::assertInstanceOf(BinaryArithmeticExpressionNode::class, $resultExpressionNode);
        self::assertSame($leftOperandExpressionNode->reveal(), $resultExpressionNode->getLeftOperandExpression());
        self::assertSame('+', $resultExpressionNode->getOperator());
        self::assertSame($rightOperandExpressionNode->reveal(), $resultExpressionNode->getRightOperandExpression());
    }

    public function testLoadReturnsAnUnknownExpressionNodeWhenParseFails()
    {
        $config = ['my' => 'invalid config'];
        $this->configParser->parseArguments($config, Argument::any())
            ->willThrow(new ArgumentParseException('Oh no, the arg parse failed!'));

        $resultExpressionNode = $this->loader->load($config);

        self::assertInstanceOf(UnknownExpressionNode::class, $resultExpressionNode);
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        self::assertSame('binary-arithmetic', $this->loader->getType());
    }
}
