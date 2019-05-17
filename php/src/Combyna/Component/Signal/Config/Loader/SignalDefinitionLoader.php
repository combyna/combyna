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

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Signal\Config\Act\InvalidSignalDefinitionNode;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNode;
use InvalidArgumentException;

/**
 * Class SignalDefinitionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionLoader implements SignalDefinitionLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var FixedStaticBagModelLoaderInterface
     */
    private $fixedStaticBagModelLoader;

    /**
     * @param ConfigParser $configParser
     * @param FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader
     */
    public function __construct(ConfigParser $configParser, FixedStaticBagModelLoaderInterface $fixedStaticBagModelLoader)
    {
        $this->configParser = $configParser;
        $this->fixedStaticBagModelLoader = $fixedStaticBagModelLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $libraryName,
        $signalName,
        $signalDefinitionConfig
    ) {
        try {
            // Ensure the config is an array or null
            $signalDefinitionConfig = $this->configParser->toArray($signalDefinitionConfig);

            $payloadStaticBagModelConfig = $this->configParser->getOptionalElement(
                $signalDefinitionConfig,
                'payload',
                'signal payload model',
                [],
                'array'
            );
            $isBroadcast = $this->configParser->getOptionalElement(
                $signalDefinitionConfig,
                'broadcast',
                'whether to broadcast the signal externally',
                false,
                'boolean'
            );
        } catch (InvalidArgumentException $exception) {
            return new InvalidSignalDefinitionNode($libraryName, $signalName, $exception->getMessage());
        }

        $payloadStaticBagModelNode = $this->fixedStaticBagModelLoader->load($payloadStaticBagModelConfig);

        return new SignalDefinitionNode($libraryName, $signalName, $payloadStaticBagModelNode, $isBroadcast);
    }
}
