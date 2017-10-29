<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Loader;

use Combyna\Component\Config\Loader\ConfigParser;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;
use Combyna\Component\Instruction\Config\Loader\InstructionCollectionLoaderInterface;
use Combyna\Component\Trigger\Config\Act\TriggerNode;

/**
 * Class TriggerLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerLoader implements TriggerLoaderInterface
{
    /**
     * @var ConfigParser
     */
    private $configParser;

    /**
     * @var InstructionCollectionLoaderInterface
     */
    private $instructionCollectionLoader;

    /**
     * @param ConfigParser $configParser
     * @param InstructionCollectionLoaderInterface $instructionCollectionLoader
     */
    public function __construct(
        ConfigParser $configParser,
        InstructionCollectionLoaderInterface $instructionCollectionLoader
    ) {
        $this->configParser = $configParser;
        $this->instructionCollectionLoader = $instructionCollectionLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function load(
        $eventLibraryName,
        $eventName,
        array $triggerConfig
    ) {
        $eventDefinitionReferenceNode = new EventDefinitionReferenceNode($eventLibraryName, $eventName);
        $instructionConfig = $this->configParser->getOptionalElement(
            $triggerConfig,
            'instructions',
            'trigger instructions',
            [],
            'array'
        );

        $instructionNodes = $this->instructionCollectionLoader->loadCollection($instructionConfig);

        return new TriggerNode($eventDefinitionReferenceNode, $instructionNodes);
    }

    /**
     * {@inheritdoc}
     */
    public function loadCollection(array $triggerConfigs)
    {
        $triggerNodes = [];

        foreach ($triggerConfigs as $eventDefinitionReferenceName => $triggerConfig) {
            list($eventLibraryName, $eventName) = explode('.', $eventDefinitionReferenceName);

            $triggerNodes[] = $this->load($eventLibraryName, $eventName, $triggerConfig);
        }

        return $triggerNodes;
    }
}
