<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Exception;

use Exception;

/**
 * Class TriggerNotFoundException
 *
 * Thrown when an attempt is made to fetch a trigger from a collection
 * when that collection contains no such trigger
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TriggerNotFoundException extends Exception
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
            sprintf(
                'Collection does not contain a trigger for event "%s" of library "%s"',
                $eventName,
                $libraryName
            )
        );

        $this->eventName = $eventName;
        $this->libraryName = $libraryName;
    }

    /**
     * Fetches the name of the event for the requested trigger
     *
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * Fetches the name of the library for the requested trigger's event
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
