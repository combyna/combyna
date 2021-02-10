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

use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionConfigParserInterface;
use Combyna\Component\Ui\Store\Config\Act\ViewStoreSlotExpressionNode;

/**
 * Class ViewStoreSlotExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ViewStoreSlotExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'slot';

    /**
     * @var ExpressionConfigParserInterface
     */
    private $configParser;

    /**
     * @param ExpressionConfigParserInterface $configParser
     */
    public function __construct(ExpressionConfigParserInterface $configParser)
    {
        $this->configParser = $configParser;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $config)
    {
        $viewStoreSlotName = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            'text',
            'slot name'
        );

        return new ViewStoreSlotExpressionNode($viewStoreSlotName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
