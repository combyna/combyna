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

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\TranslationExpressionNode;
use Combyna\Component\Expression\TranslationExpression;

/**
 * Class TranslationExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TranslationExpressionLoader implements ExpressionTypeLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @param ConfigParser $configParser
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     */
    public function __construct(
        ConfigParser $configParser,
        ExpressionBagLoaderInterface $expressionBagLoader
    ) {
        $this->configParser = $configParser;
        $this->expressionBagLoader = $expressionBagLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $translationKey = $this->configParser->getElement($config, 'key', 'translation expression');
        $parameterBagConfig = $this->configParser->getOptionalElement($config, 'parameters', 'translation expression');

        $parameterBagNode = $parameterBagConfig !== null ?
            $this->expressionBagLoader->load($parameterBagConfig) :
            null;

        return new TranslationExpressionNode(
            $translationKey,
            $parameterBagNode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return TranslationExpression::TYPE;
    }
}
