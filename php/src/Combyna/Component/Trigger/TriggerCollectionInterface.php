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

use Combyna\Component\Trigger\Exception\TriggerNotFoundException;

/**
 * Interface TriggerCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TriggerCollectionInterface
{
    /**
     * Fetches all triggers in this collection
     *
     * @return TriggerInterface[]
     */
    public function getAll();

    /**
     * Fetches a trigger from this collection by the name of the event it fires on
     *
     * @param string $libraryName
     * @param string $eventName
     * @return TriggerInterface
     * @throws TriggerNotFoundException Throws when no trigger is defined for the given event
     */
    public function getByEventName($libraryName, $eventName);

    /**
     * Determines whether this collection contains a trigger for the specified event
     *
     * @param string $libraryName
     * @param string $eventName
     * @return bool
     */
    public function hasByEventName($libraryName, $eventName);

    /**
     * Returns true if this collection contains no triggers, false otherwise
     *
     * @return bool
     */
    public function isEmpty();
}
