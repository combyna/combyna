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
use Combyna\Component\Expression\Config\Act\ExpressionNodeInterface;
use Combyna\Component\Expression\Config\Act\MapExpressionNode;
use Combyna\Component\Expression\Config\Loader\ExpressionLoaderInterface;
use Combyna\Component\Expression\Config\Loader\MapExpressionLoader;
use Combyna\Harness\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class MapExpressionLoaderTest
 *
 * @author Robin Cawser
 */
class MapExpressionLoaderTest extends TestCase
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
     * @var MapExpressionLoader
     */
    private $loader;

    public function setUp()
    {
        $this->configParser = $this->prophesize(ConfigParser::class);
        $this->configParser->getElement(Argument::any(), Argument::any(), Argument::any(), Argument::any())
            ->will(function (array $args) {
                return $args[0][$args[1]];
            });

        $this->expressionLoader = $this->prophesize(ExpressionLoaderInterface::class);
        $this->loader = new MapExpressionLoader(
            $this->configParser->reveal(),
            $this->expressionLoader->reveal()
        );
    }

    public function testLoadReturnsACorrectlyBuildMapExpressionNode()
    {
        $config = [
            'type' => 'map',
            'map' => [
                'type' => 'text',
                'text' => 'mapped replacement'
            ],
            'item_variable' => 'my_item',
            'index_variable' => 'my_index',
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
        ];
        $listExpression = $this->prophesize(ExpressionNodeInterface::class);
        $mapExpression = $this->prophesize(ExpressionNodeInterface::class);
        $this->expressionLoader->load($config['list'])->willReturn($listExpression->reveal());
        $this->expressionLoader->load($config['map'])->willReturn($mapExpression->reveal());

        $resultMapExpressionNode = $this->loader->load($config);

        $this->assert($resultMapExpressionNode)->isAnInstanceOf(MapExpressionNode::class);
        $this->assert($resultMapExpressionNode->getMapExpression())->isTheSameAs($mapExpression->reveal());
        $this->assert($resultMapExpressionNode->getListExpression())->isTheSameAs($listExpression->reveal());
        $this->assert($resultMapExpressionNode->getIndexVariableName())->equals('my_index');
        $this->assert($resultMapExpressionNode->getItemVariableName())->equals('my_item');
    }

    public function testGetTypeReturnsTheCorrectType()
    {
        $this->assert($this->loader->getType())->exactlyEquals('map');
    }
}
