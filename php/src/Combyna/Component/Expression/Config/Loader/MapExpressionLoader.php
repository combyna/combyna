<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\MapExpressionNode;
use Combyna\Component\Expression\MapExpression;

/**
 * Class MapExpressionLoader
 *
 * @author Robin Cawser
 */
class MapExpressionLoader implements ExpressionTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ConfigParser $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     */
    public function __construct(ConfigParser $configParser, ExpressionLoaderInterface $expressionLoader)
    {
        $this->configParser = $configParser;
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $itemVariable = $this->configParser->getElement($config, 'item_variable', 'map expression', 'string');
        $indexVariable = $this->configParser->getElement($config, 'index_variable', 'map expression', 'string');
        $list = $this->configParser->getElement($config, 'list', 'map expression', 'array');
        $map = $this->configParser->getElement($config, 'map', 'map expression', 'array');

        $listExpressionNode = $this->expressionLoader->load($list);
        $mapExpressionNode = $this->expressionLoader->load($map);

        return new MapExpressionNode(
            $listExpressionNode,
            $itemVariable,
            $indexVariable,
            $mapExpressionNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return MapExpression::TYPE;
    }
}
