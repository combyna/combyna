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

use Combyna\Component\Event\Exception\EventDefinitionNotFoundException;

/**
 * Interface EventDefinitionCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionCollectionInterface
{
    /**
     * Fetches an event definition from this collection by its unique name
     *
     * @param string $eventName
     * @return EventDefinitionInterface
     * @throws EventDefinitionNotFoundException Throws when no definition has the specified name
     */
    public function getByName($eventName);
}
