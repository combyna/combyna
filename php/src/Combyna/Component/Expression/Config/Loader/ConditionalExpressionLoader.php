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
use Combyna\Component\Expression\ConditionalExpression;
use Combyna\Component\Expression\Config\Act\ConditionalExpressionNode;

/**
 * Class ConditionalExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalExpressionLoader implements ExpressionTypeLoaderInterface
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
        $conditionalConfig = $this->configParser->getElement($config, 'conditional', 'comparison expression', 'array');
        $consequentConfig = $this->configParser->getElement($config, 'consequent', 'comparison expression', 'array');
        $alternateConfig = $this->configParser->getElement($config, 'alternate', 'comparison expression', 'array');

        $conditionalExpressionNode = $this->expressionLoader->load($conditionalConfig);
        $consequentExpressionNode = $this->expressionLoader->load($consequentConfig);
        $alternateExpressionNode = $this->expressionLoader->load($alternateConfig);

        return new ConditionalExpressionNode(
            $conditionalExpressionNode,
            $consequentExpressionNode,
            $alternateExpressionNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return ConditionalExpression::TYPE;
    }
}
