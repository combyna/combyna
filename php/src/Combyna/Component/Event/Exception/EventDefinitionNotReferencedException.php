<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Exception;

use Exception;

/**
 * Class EventDefinitionNotReferencedException
 *
 * Thrown when an attempt is made to fetch an event definition from a collection of references
 * when that collection does not contain the specified definition.
 * Note that the definition may actually exist, just not be referenced by the collection.
 * (Widget definitions define a list of events they support)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionNotReferencedException extends Exception
{
    /**
     * @var string
     */
    private $eventName;

    /**
     * @var string
     */
    private $libraryName;

    /**
     * @param string $libraryName
     * @param string $eventName
     */
    public function __construct($libraryName, $eventName)
    {
        parent::__construct(
            'Event definition "' . $eventName . '" for library "' . $libraryName . '" is not referenced'
        );

        $this->eventName = $eventName;
        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the name of the unreferenced event definition
     *
     * @return string
     */
    public function getEventDefinitionName()
    {
        return $this->eventName;
    }

    /**
     * Fetches the name of the requested event definition's library
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
