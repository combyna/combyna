<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger;

use Combyna\Component\Event\EventDefinitionReferenceInterface;
use Combyna\Component\Trigger\Instruction\InstructionListInterface;

/**
 * Interface TriggerFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TriggerFactoryInterface
{
    /**
     * Creates a new Trigger
     *
     * @param EventDefinitionReferenceInterface $eventDefinitionReference
     * @param InstructionListInterface $instructionList
     * @return TriggerInterface
     */
    public function createTrigger(
        EventDefinitionReferenceInterface $eventDefinitionReference,
        InstructionListInterface $instructionList
    );

    /**
     * Creates a new TriggerCollection
     *
     * @param TriggerInterface[] $triggers
     * @return TriggerCollectionInterface
     */
    public function createTriggerCollection(array $triggers);
}
