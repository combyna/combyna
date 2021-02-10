<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Loader;

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionConfigParserInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreQueryExpressionNode;

/**
 * Class ViewStoreQueryExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreQueryExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'view_query';

    /**
     * @var ExpressionConfigParserInterface
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @param ExpressionConfigParserInterface $configParser
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     */
    public function __construct(
        ExpressionConfigParserInterface $configParser,
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
        $viewStoreQueryName = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            'text',
            'query name'
        );
        $argumentBagNode = $this->configParser->getNamedArgumentStaticBag($config);

        return new ViewStoreQueryExpressionNode(
            $viewStoreQueryName,
            $argumentBagNode
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
