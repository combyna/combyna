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

use Combyna\Component\Expression\Config\Act\ConversionExpressionNode;
use Combyna\Component\Expression\ConversionExpression;
use Combyna\Component\Config\Loader\ConfigParser;

/**
 * Class TextConversionExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextConversionExpressionLoader implements BuiltinLoaderInterface
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
        $expressionConfig = $this->configParser->getElement(
            $config,
            'expression',
            'number-to-text conversion expression'
        );

        $expressionNode = $this->expressionLoader->load($expressionConfig);

        return new ConversionExpressionNode(
            $expressionNode,
            ConversionExpression::NUMBER_TO_TEXT
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return 'text';
    }
}
