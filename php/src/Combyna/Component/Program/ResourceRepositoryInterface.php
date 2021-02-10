<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Program;

use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Event\EventDefinitionInterface;
use Combyna\Component\Event\Exception\EventDefinitionNotFoundException;
use Combyna\Component\Signal\Exception\SignalDefinitionNotFoundException;
use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;

/**
 * Interface ResourceRepositoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ResourceRepositoryInterface
{
    /**
     * Fetches an event definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $eventName
     * @return EventDefinitionInterface
     * @throws EventDefinitionNotFoundException
     */
    public function getEventDefinitionByName($libraryName, $eventName);

    /**
     * Fetches a signal definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $signalName
     * @return SignalDefinitionInterface
     * @throws SignalDefinitionNotFoundException
     */
    public function getSignalDefinitionByName($libraryName, $signalName);

    /**
     * Fetches a widget definition with the given name from a library in the environment
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     * @throws WidgetDefinitionNotSupportedException
     */
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName);
}
