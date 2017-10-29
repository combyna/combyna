<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Act;

use Combyna\Component\Environment\EnvironmentInterface;
use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventDefinitionReferenceInterface;
use Combyna\Component\Event\EventFactoryInterface;

/**
 * Class EventDefinitionReferenceNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionReferenceNodePromoter
{
    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @param EventFactoryInterface $eventFactory
     */
    public function __construct(EventFactoryInterface $eventFactory)
    {
        $this->eventFactory = $eventFactory;
    }

    /**
     * Promotes an EventDefinitionReferenceNode to an EventDefinitionReference
     *
     * @param EventDefinitionReferenceNode $eventDefinitionReferenceNode
     * @return EventDefinitionReferenceInterface
     */
    public function promote(EventDefinitionReferenceNode $eventDefinitionReferenceNode)
    {
        return $this->eventFactory->createEventDefinitionReference(
            $eventDefinitionReferenceNode->getLibraryName(),
            $eventDefinitionReferenceNode->getEventName()
        );
    }

    /**
     * Promotes a set of EventDefinitionReferenceNodes to an array of EventDefinitionReferences
     *
     * @param EventDefinitionReferenceNode[] $eventDefinitionReferenceNodes
     * @param EnvironmentInterface $environment
     * @return EventDefinitionReferenceCollectionInterface
     */
    public function promoteCollection(array $eventDefinitionReferenceNodes, EnvironmentInterface $environment)
    {
        /** @var EventDefinitionReferenceInterface[] $eventDefinitionReferences */
        $eventDefinitionReferences = [];

        foreach ($eventDefinitionReferenceNodes as $eventDefinitionReferenceNode) {
            $eventDefinitionReferences[] = $this->promote($eventDefinitionReferenceNode);
        }

        return $this->eventFactory->createEventDefinitionReferenceCollection(
            $eventDefinitionReferences,
            $environment
        );
    }
}
