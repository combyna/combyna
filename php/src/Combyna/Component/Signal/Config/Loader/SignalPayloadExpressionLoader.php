<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Loader;

use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionConfigParserInterface;
use Combyna\Component\Signal\Config\Act\SignalPayloadExpressionNode;

/**
 * Class SignalPayloadExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalPayloadExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'signal_payload';

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
        $payloadStaticName = $this->configParser->getPositionalArgumentNative(
            $config,
            0,
            'text',
            'payload static name'
        );

        return new SignalPayloadExpressionNode($payloadStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
