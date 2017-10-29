<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

/**
 * Class EventDefinitionReferenceCollection
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionReferenceCollectionInterface
{
    /**
     * Fetches an event definition referenced by this collection
     *
     * @param string $libraryName
     * @param string $eventName
     * @return EventDefinitionInterface
     * @throws EventDefinitionNotReferencedException When not referenced
     */
    public function getDefinitionByName($libraryName, $eventName);
}
