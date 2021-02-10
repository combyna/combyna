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

use Combyna\Component\Config\Loader\ExpressionConfigParser;
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
     * @var ExpressionConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionLoaderInterface
     */
    private $expressionLoader;

    /**
     * @param ExpressionConfigParser $configParser
     * @param ExpressionLoaderInterface $expressionLoader
     */
    public function __construct(
        ExpressionConfigParser $configParser,
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
        $expressionNode = $this->configParser->getPositionalArgument(
            $config,
            0,
            'text-to-number conversion expression'
        );

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
