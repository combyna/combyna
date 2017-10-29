<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Expression\Config\Loader;

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Expression\Config\Act\FunctionExpressionNode;
use Combyna\Component\Expression\FunctionExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class FunctionExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FunctionExpressionLoader implements ExpressionTypeLoaderInterface
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
    public function __construct(
        ConfigParser $configParser,
        ExpressionLoaderInterface $expressionLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionLoader = $expressionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $libraryName = $this->configParser->getElement($config, 'library', 'function expression', 'array');
        $functionName = $this->configParser->getElement($config, 'name', 'function expression');
        $argumentConfigs = $this->configParser->getElement($config, 'arguments', 'function expression', 'array');

        $argumentExpressionNodes = [];

        foreach ($argumentConfigs as $parameterName => $argumentConfig) {
            $argumentExpressionNodes[$parameterName] = $this->expressionLoader->load($argumentConfig);
        }

        return new FunctionExpressionNode(
            $libraryName,
            $functionName,
            new ExpressionBagNode($argumentExpressionNodes)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return FunctionExpression::TYPE;
    }
}
