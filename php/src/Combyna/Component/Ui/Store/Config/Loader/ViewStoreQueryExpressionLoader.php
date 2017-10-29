<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Loader;

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Loader\ExpressionConfigParser;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
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
     * @var ExpressionConfigParser
     */
    private $configParser;

    /**
     * @var ExpressionBagLoaderInterface
     */
    private $expressionBagLoader;

    /**
     * @param ExpressionConfigParser $configParser
     * @param ExpressionBagLoaderInterface $expressionBagLoader
     */
    public function __construct(
        ExpressionConfigParser $configParser,
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
