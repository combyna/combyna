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

/**
 * Class TriggerCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerCollection implements TriggerCollectionInterface
{
    /**
     * @var TriggerInterface[]
     */
    private $triggers;

    /**
     * @param TriggerInterface[] $triggers
     */
    public function __construct(array $triggers)
    {
        $this->triggers = $triggers;
    }

    /**
     * {@inheritdoc}
     */
    public function getByEventName($libraryName, $eventName)
    {
        foreach ($this->triggers as $trigger) {
            if ($trigger->getEventLibraryName() === $libraryName && $trigger->getEventName() === $eventName) {
                return $trigger;
            }
        }

        throw new TriggerNotFoundException();
    }

    /**
     * {@inheritdoc}
     */
    public function hasByEventName($libraryName, $eventName)
    {
        foreach ($this->triggers as $trigger) {
            if ($trigger->getEventLibraryName() === $libraryName && $trigger->getEventName() === $eventName) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        return empty($this->triggers);
    }
}
