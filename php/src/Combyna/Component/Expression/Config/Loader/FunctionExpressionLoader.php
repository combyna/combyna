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

use Combyna\Component\Bag\Config\Act\ExpressionBagNode;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\FunctionExpressionNode;
use Combyna\Component\Expression\FunctionExpression;

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
        $libraryName = $this->configParser->getElement($config, 'library', 'function expression', 'string');
        $functionName = $this->configParser->getElement($config, 'name', 'function expression', 'string');
        $argumentConfigs = $this->configParser->getElement($config, 'arguments', 'function expression', ['array', 'string']);

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
