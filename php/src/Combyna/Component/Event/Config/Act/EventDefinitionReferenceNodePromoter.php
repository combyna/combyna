<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Act;

use Combyna\Component\Event\EventDefinitionReferenceCollectionInterface;
use Combyna\Component\Event\EventDefinitionReferenceInterface;
use Combyna\Component\Event\EventFactoryInterface;
use Combyna\Component\Program\ResourceRepositoryInterface;

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
     * @param ResourceRepositoryInterface $resourceRepository
     * @return EventDefinitionReferenceCollectionInterface
     */
    public function promoteCollection(
        array $eventDefinitionReferenceNodes,
        ResourceRepositoryInterface $resourceRepository
    ) {
        /** @var EventDefinitionReferenceInterface[] $eventDefinitionReferences */
        $eventDefinitionReferences = [];

        foreach ($eventDefinitionReferenceNodes as $eventDefinitionReferenceNode) {
            $eventDefinitionReferences[] = $this->promote($eventDefinitionReferenceNode);
        }

        return $this->eventFactory->createEventDefinitionReferenceCollection(
            $eventDefinitionReferences,
            $resourceRepository
        );
    }
}
