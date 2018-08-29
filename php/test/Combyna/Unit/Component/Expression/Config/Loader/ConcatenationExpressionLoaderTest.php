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
use Combyna\Component\Expression\Config\Act\ConcatenationExpressionNode;
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Loader\ConcatenationExpressionLoader;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ConcatenationExpressionLoaderTest
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConcatenationExpressionLoaderTest extends TestCase
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
     * @var ConcatenationExpressionLoader
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
        $this->configParser->getOptionalElement(Argument::any(), Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[0][$args[1]];
            });

        $this->loader = new ConcatenationExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionLoader->reveal()
        );
    }

    public function testLoadReturnsACorrectlyBuiltConcatenationExpressionNode()
    {
        $config = [
            'type' => 'concatenation',
            'list' => [
                'type' => 'list',
                'elements' => [
                    [
                        'type' => 'text',
                        'text' => 'first'
                    ],
                    [
                        'type' => 'text',
                        'text' => 'second'
                    ]
                ]
            ],
            'glue' => [
                'type' => 'text',
                'text' => '|'
            ]
        ];
        $listExpression = $this->prophesize(ExpressionNodeInterface::class);
        $glueExpression = $this->prophesize(ExpressionNodeInterface::class);
        $this->expressionLoader->load($config['list'])->willReturn($listExpression->reveal());
        $this->expressionLoader->load($config['glue'])->willReturn($glueExpression->reveal());

        $resultExpressionNode = $this->loader->load($config);

        $this->assert($resultExpressionNode)->isAnInstanceOf(ConcatenationExpressionNode::class);
        $this->assert($resultExpressionNode->getOperandListExpression())->isTheSameAs($listExpression->reveal());
        $this->assert($resultExpressionNode->getGlueExpression())->isTheSameAs($glueExpression->reveal());
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('concatenation');
    }
}
