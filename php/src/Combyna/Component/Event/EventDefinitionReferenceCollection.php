<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

use Combyna\Component\Event\Exception\EventDefinitionNotReferencedException;
use Combyna\Component\Program\ResourceRepositoryInterface;

/**
 * Class EventDefinitionReferenceCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionReferenceCollection implements EventDefinitionReferenceCollectionInterface
{
    /**
     * @var EventDefinitionReferenceInterface[]
     */
    private $eventDefinitionReferences = [];

    /**
     * @var ResourceRepositoryInterface
     */
    private $resourceRepository;

    /**
     * @param EventDefinitionReferenceInterface[] $eventDefinitionReferences
     * @param ResourceRepositoryInterface $resourceRepository
     */
    public function __construct(array $eventDefinitionReferences, ResourceRepositoryInterface $resourceRepository)
    {
        $this->eventDefinitionReferences = $eventDefinitionReferences;
        $this->resourceRepository = $resourceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinitionByName($libraryName, $eventName)
    {
        foreach ($this->eventDefinitionReferences as $eventDefinitionReference) {
            if ($eventDefinitionReference->getLibraryName() === $libraryName &&
                $eventDefinitionReference->getEventName() === $eventName
            ) {
                return $this->resourceRepository->getEventDefinitionByName(
                    $eventDefinitionReference->getLibraryName(),
                    $eventDefinitionReference->getEventName()
                );
            }
        }

        // This collection does not mention the specified event definition,
        // so it cannot be fetched
        // (Widget definitions define a list of events they support)
        throw new EventDefinitionNotReferencedException($libraryName, $eventName);
    }
}
