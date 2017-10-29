<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Loader;

use Combyna\Component\Bag\Config\Loader\FixedStaticBagModelLoaderInterface;
use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Event\Config\Act\EventDefinitionNode;

/**
 * Class EventDefinitionLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionLoader implements EventDefinitionLoaderInterface
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
        $eventName,
        array $eventDefinitionConfig
    ) {
        $payloadStaticBagModelConfig = $this->configParser->getOptionalElement(
            $eventDefinitionConfig,
            'payload',
            'event payload model',
            [],
            'array'
        );

        $payloadStaticBagModelNode = $this->fixedStaticBagModelLoader->load($payloadStaticBagModelConfig);

        return new EventDefinitionNode($eventName, $payloadStaticBagModelNode);
    }
}
