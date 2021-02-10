<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Config\Loader;

use Combyna\Component\Config\Loader\ExpressionConfigParser;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Ui\Config\Act\Expression\WidgetValueExpressionNode;

/**
 * Class WidgetValueExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetValueExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'widget_value';

    /**
     * @var ExpressionConfigParser
     */
    private $configParser;

    /**
     * @param ExpressionConfigParser $configParser
     */
    public function __construct(ExpressionConfigParser $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $widgetValueName = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            'text',
            'widget value name'
        );

        return new WidgetValueExpressionNode($widgetValueName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
