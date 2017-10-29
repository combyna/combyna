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

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Expression\Config\Act\ConversionExpressionNode;
use Combyna\Component\Expression\ConversionExpression;

/**
 * Class NumberConversionExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class NumberConversionExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'number';

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
            'text-to-number conversion expression'
        );

        $expressionNode = $this->expressionLoader->load($expressionConfig);

        return new ConversionExpressionNode(
            $expressionNode,
            ConversionExpression::TEXT_TO_NUMBER
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
