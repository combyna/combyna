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
 * Class EventDefinitionReference
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionReference implements EventDefinitionReferenceInterface
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
        $this->eventName = $eventName;
        $this->libraryName = $libraryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLibraryName()
    {
        return $this->libraryName;
    }
}
