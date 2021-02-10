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

use Combyna\Component\Environment\Exception\LibraryNotInstalledException;
use Combyna\Component\Event\Exception\EventDefinitionNotFoundException;
use Combyna\Component\Event\Exception\EventDefinitionNotReferencedException;

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
     * @throws EventDefinitionNotFoundException When not found
     * @throws EventDefinitionNotReferencedException When not referenced
     * @throws LibraryNotInstalledException
     */
    public function getDefinitionByName($libraryName, $eventName);
}
