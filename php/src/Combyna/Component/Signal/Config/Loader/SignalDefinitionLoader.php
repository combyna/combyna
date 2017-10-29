<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Loader;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Signal\Config\Act\SignalDefinitionNode;

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
        array $signalDefinitionConfig
    ) {
        $payloadStaticBagModelConfig = $this->configParser->getOptionalElement(
            $signalDefinitionConfig,
            'payload',
            'signal payload model',
            [],
            'array'
        );

        $payloadStaticBagModelNode = $this->fixedStaticBagModelLoader->load($payloadStaticBagModelConfig);

        return new SignalDefinitionNode($signalName, $payloadStaticBagModelNode);
    }
}
