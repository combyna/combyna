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

use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Expression\Config\Act\ComparisonExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\ExpressionFactoryInterface;
use Combyna\Component\Expression\ExpressionInterface;
use Combyna\Harness\TestCase;
use Combyna\Component\Expression\Config\Loader\ComparisonExpressionLoader;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ComparisonExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpressionLoaderTest extends TestCase
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
     * @var ComparisonExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->expressionLoader = $this->prophesize(ExpressionLoaderInterface::class);

        $this->configParser->getElement(Argument::any(), Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[0][$args[1]];
            });

        $this->loader = new ComparisonExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionLoader->reveal()
        );
    }

    public function testLoadReturnsACorrectlyBuiltComparisonExpressionNode()
    {
        $config = [
            'type' => 'comparison',
            'left' => [
                'type' => 'number',
                'number' => 21
            ],
            'operator' => '<>',
            'right' => [
                'type' => 'number',
                'number' => 101.99
            ]
        ];
        $leftOperandExpression = $this->prophesize(ExpressionNodeInterface::class);
        $rightOperandExpression = $this->prophesize(ExpressionNodeInterface::class);
        $this->expressionLoader->load($config['left'])->willReturn($leftOperandExpression->reveal());
        $this->expressionLoader->load($config['right'])->willReturn($rightOperandExpression->reveal());

        $resultExpressionNode = $this->loader->load($config);

        $this->assert($resultExpressionNode)->isAnInstanceOf(ComparisonExpressionNode::class);
        $this->assert($resultExpressionNode->getLeftOperandExpression())->isTheSameAs($leftOperandExpression->reveal());
        $this->assert($resultExpressionNode->getOperator())->isTheSameAs('<>');
        $this->assert($resultExpressionNode->getRightOperandExpression())->isTheSameAs($rightOperandExpression->reveal());
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('comparison');
    }
}
