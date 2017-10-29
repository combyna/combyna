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

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Event\EventDefinitionCollectionInterface;
use Combyna\Component\Event\EventDefinitionInterface;
use Combyna\Component\Event\EventFactoryInterface;

/**
 * Class EventDefinitionNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var EventFactoryInterface
     */
    private $eventFactory;

    /**
     * @param EventFactoryInterface $eventFactory
     * @param BagNodePromoter $bagNodePromoter
     */
    public function __construct(EventFactoryInterface $eventFactory, BagNodePromoter $bagNodePromoter)
    {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->eventFactory = $eventFactory;
    }

    /**
     * Promotes an EventDefinitionNode to an EventDefinition
     *
     * @param EventDefinitionNode $eventDefinitionNode
     * @param string $libraryName
     * @return EventDefinitionInterface
     */
    public function promote(EventDefinitionNode $eventDefinitionNode, $libraryName)
    {
        return $this->eventFactory->createEventDefinition(
            $libraryName,
            $eventDefinitionNode->getEventName(),
            $this->bagNodePromoter->promoteFixedStaticBagModel($eventDefinitionNode->getPayloadStaticBagModel())
        );
    }

    /**
     * Promotes a set of EventDefinitionNodes to a EventDefinitionCollection
     *
     * @param EventDefinitionNode[] $eventDefinitionNodes
     * @param string $libraryName
     * @return EventDefinitionCollectionInterface
     */
    public function promoteCollection(array $eventDefinitionNodes, $libraryName)
    {
        $eventDefinitions = [];

        foreach ($eventDefinitionNodes as $eventDefinitionNode) {
            $eventDefinitions[] = $this->promote($eventDefinitionNode, $libraryName);
        }

        return $this->eventFactory->createEventDefinitionCollection($eventDefinitions, $libraryName);
    }
}
