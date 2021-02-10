<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Event\Exception;

use Exception;

/**
 * Class EventDefinitionNotReferencedByWidgetException
 *
 * Thrown when an attempt is made to fetch an event definition from a collection of references
 * when that collection does not contain the specified definition.
 * Note that the definition may actually exist, just not be referenced by the collection.
 * (Widget definitions define a list of events they support)
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionNotReferencedByWidgetException extends Exception
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
     * @var string
     */
    private $widgetDefinitionName;

    /**
     * @var string
     */
    private $widgetLibraryName;

    /**
     * @param string $libraryName
     * @param string $eventName
     * @param string $widgetLibraryName
     * @param string $widgetDefinitionName
     * @param Exception $previousException
     */
    public function __construct(
        $libraryName,
        $eventName,
        $widgetLibraryName,
        $widgetDefinitionName,
        Exception $previousException = null
    ) {
        parent::__construct(
            sprintf(
                'Event definition "%s" for library "%s" is not referenced by widget "%s" for library "%s"',
                $eventName,
                $libraryName,
                $widgetDefinitionName,
                $widgetLibraryName
            ),
            0,
            $previousException
        );

        $this->eventName = $eventName;
        $this->libraryName = $libraryName;
        $this->widgetLibraryName = $widgetLibraryName;
        $this->widgetDefinitionName = $widgetDefinitionName;
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

    /**
     * Fetches the name of the widget definition that does not contain the reference
     *
     * @return string
     */
    public function getWidgetDefinitionName()
    {
        return $this->widgetDefinitionName;
    }

    /**
     * Fetches the name of the library containing the widget definition
     *
     * @return string
     */
    public function getWidgetLibraryName()
    {
        return $this->widgetLibraryName;
    }
}
