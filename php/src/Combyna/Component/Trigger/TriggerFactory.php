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
 * Class TriggerFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerFactory implements TriggerFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createTrigger(
        EventDefinitionReferenceInterface $eventDefinitionReference,
        InstructionListInterface $instructionList
    ) {
        return new Trigger($eventDefinitionReference, $instructionList);
    }

    /**
     * {@inheritdoc}
     */
    public function createTriggerCollection(array $triggers)
    {
        return new TriggerCollection($triggers);
    }
}
