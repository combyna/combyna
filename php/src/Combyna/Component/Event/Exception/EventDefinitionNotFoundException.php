<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Exception;

use Exception;

/**
 * Class EventDefinitionNotFoundException
 *
 * Thrown when an attempt is made to fetch an event definition from a library
 * when that library implements no such event definition
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionNotFoundException extends Exception
{
    /**
     * @var string
     */
    private $libraryName;

    /**
     * @var string
     */
    private $eventName;

    /**
     * @param string $libraryName
     * @param string $eventName
     */
    public function __construct($libraryName, $eventName)
    {
        parent::__construct(
            'Library "' . $libraryName . '" does not define event definition "' . $eventName . '"'
        );

        $this->libraryName = $libraryName;
        $this->eventName = $eventName;
    }

    /**
     * Fetches the name of the library that does not support the requested event definition
     *
     * @return string
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }

    /**
     * Fetches the name of the unsupported event definition
     *
     * @return string
     */
    public function getEventDefinitionName()
    {
        return $this->eventName;
    }
}
