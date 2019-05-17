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

/**
 * Interface EventDefinitionRepositoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionRepositoryInterface
{
    /**
     * Fetches an event definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $eventName
     * @return EventDefinitionInterface
     */
    public function getByName($libraryName, $eventName);
}
