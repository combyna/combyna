<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Act;

use Combyna\Component\Bag\Config\Act\BagNodePromoter;
use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNodePromoter;
use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Trigger\TriggerCollectionInterface;
use Combyna\Component\Trigger\TriggerFactoryInterface;

/**
 * Class TriggerNodePromoter
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerNodePromoter
{
    /**
     * @var BagNodePromoter
     */
    private $bagNodePromoter;

    /**
     * @var EventDefinitionReferenceNodePromoter
     */
    private $eventDefinitionReferenceNodePromoter;

    /**
     * @var InstructionNodePromoterInterface
     */
    private $instructionNodePromoter;

    /**
     * @var TriggerFactoryInterface
     */
    private $triggerFactory;

    /**
     * @param TriggerFactoryInterface $triggerFactory
     * @param BagNodePromoter $bagNodePromoter
     * @param EventDefinitionReferenceNodePromoter $eventDefinitionReferenceNodePromoter
     * @param InstructionNodePromoterInterface $instructionNodePromoter
     */
    public function __construct(
        TriggerFactoryInterface $triggerFactory,
        BagNodePromoter $bagNodePromoter,
        EventDefinitionReferenceNodePromoter $eventDefinitionReferenceNodePromoter,
        InstructionNodePromoterInterface $instructionNodePromoter
    ) {
        $this->bagNodePromoter = $bagNodePromoter;
        $this->eventDefinitionReferenceNodePromoter = $eventDefinitionReferenceNodePromoter;
        $this->instructionNodePromoter = $instructionNodePromoter;
        $this->triggerFactory = $triggerFactory;
    }

    /**
     * Promotes a set of TriggerNodes to a TriggerCollection
     *
     * @param TriggerNode[] $triggerNodes
     * @param ResourceRepositoryInterface $resourceRepository
     * @return TriggerCollectionInterface
     */
    public function promoteCollection(array $triggerNodes, ResourceRepositoryInterface $resourceRepository)
    {
        $triggers = [];

        foreach ($triggerNodes as $triggerNode) {
            $eventDefinitionReference = $this->eventDefinitionReferenceNodePromoter->promote(
                $triggerNode->getEventDefinitionReference()
            );

            $triggers[] = $this->triggerFactory->createTrigger(
                $eventDefinitionReference,
                $this->instructionNodePromoter->promoteList($triggerNode->getInstructions(), $resourceRepository)
            );
        }

        return $this->triggerFactory->createTriggerCollection($triggers);
    }
}
