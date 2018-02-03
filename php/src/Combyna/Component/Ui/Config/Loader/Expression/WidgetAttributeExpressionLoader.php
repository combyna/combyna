<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader\Expression;

use Combyna\Component\Bag\Config\Loader\ExpressionBagLoaderInterface;
use Combyna\Component\Config\Loader\ExpressionConfigParser;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Ui\Config\Act\Expression\WidgetAttributeExpressionNode;

/**
 * Class WidgetAttributeExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetAttributeExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'widget_attr';

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
        $attributeName = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            'text',
            'attribute name'
        );

        return new WidgetAttributeExpressionNode($attributeName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
