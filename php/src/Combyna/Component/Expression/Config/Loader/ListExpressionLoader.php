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

use Combyna\Component\Bag\Config\Act\ExpressionListNode;
use Combyna\Component\Expression\Config\Act\ListExpressionNode;
use Combyna\Component\Expression\ListExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class ListExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ListExpressionLoader implements ExpressionTypeLoaderInterface
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
        $elementConfigs = $this->configParser->getElement($config, 'elements', 'list expression');
        $elementExpressionNodes = [];

        foreach ($elementConfigs as $elementConfig) {
            $elementExpressionNodes[] = $this->expressionLoader->load($elementConfig);
        }

        return new ListExpressionNode(
            new ExpressionListNode($elementExpressionNodes)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return ListExpression::TYPE;
    }
}
