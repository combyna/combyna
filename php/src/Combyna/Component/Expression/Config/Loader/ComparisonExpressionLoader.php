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

use Combyna\Component\Expression\Config\Act\ComparisonExpressionNode;
use Combyna\Component\Expression\ComparisonExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class ComparisonExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ComparisonExpressionLoader implements ExpressionTypeLoaderInterface
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
        $leftOperandConfig = $this->configParser->getElement($config, 'left', 'comparison expression', 'array');
        $operator = $this->configParser->getElement($config, 'operator', 'comparison expression');
        $rightOperandConfig = $this->configParser->getElement($config, 'right', 'comparison expression', 'array');

        $leftOperandExpressionNode = $this->expressionLoader->load($leftOperandConfig);
        $rightOperandExpressionNode = $this->expressionLoader->load($rightOperandConfig);

        return new ComparisonExpressionNode(
            $leftOperandExpressionNode,
            $operator,
            $rightOperandExpressionNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return ComparisonExpression::TYPE;
    }
}
