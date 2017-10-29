<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

/**
 * Class EventDefinitionCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionCollection implements EventDefinitionCollectionInterface
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var EventDefinitionInterface[]
     */
    private $eventDefinitions = [];

    /**
     * @param EventDefinitionInterface[] $eventDefinitions
     * @param string $libraryName
     */
    public function __construct(array $eventDefinitions, $libraryName)
    {
        $this->libraryName = $libraryName;

        // Index the event definitions by name to simplify lookups
        foreach ($eventDefinitions as $eventDefinition) {
            $this->eventDefinitions[$eventDefinition->getName()] = $eventDefinition;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getByName($eventName)
    {
        if (!array_key_exists($eventName, $this->eventDefinitions)) {
            throw new EventDefinitionNotFoundException($this->libraryName, $eventName);
        }

        return $this->eventDefinitions[$eventName];
    }
}
