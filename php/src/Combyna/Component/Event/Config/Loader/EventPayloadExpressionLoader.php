<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Loader;

use Combyna\Component\Event\Config\Act\EventPayloadExpressionNode;
use Combyna\Component\Expression\Config\Loader\BuiltinLoaderInterface;
use Combyna\Component\Expression\Config\Loader\ExpressionConfigParserInterface;

/**
 * Class EventPayloadExpressionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventPayloadExpressionLoader implements BuiltinLoaderInterface
{
    const BUILTIN_NAME = 'event_payload';

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

        return new EventPayloadExpressionNode($payloadStaticName);
    }

    /**
     * {@inheritdoc}
     */
    public function getBuiltinName()
    {
        return self::BUILTIN_NAME;
    }
}
