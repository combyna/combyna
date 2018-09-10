<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Environment\Library;

use Combyna\Component\Environment\Exception\FunctionNotSupportedException;
use Combyna\Component\Environment\Exception\IncorrectFunctionTypeException;
use Combyna\Component\Environment\Exception\WidgetDefinitionNotSupportedException;
use Combyna\Component\Event\EventDefinitionInterface;
use Combyna\Component\Event\Exception\EventDefinitionNotFoundException;
use Combyna\Component\Signal\Exception\SignalDefinitionNotFoundException;
use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Plugin\Core\CorePlugin;

/**
 * Interface LibraryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface LibraryInterface
{
    /**
     * The unique name for the special "app" library
     * that represents resources (routes, signals, views etc.) for the current app
     */
    const APP = 'app';

    /**
     * The unique name for the special "core" library
     * that represents resources (routes, signals, views etc.) that are built-in.
     * For example, the "navigated" core signal is dispatched upon navigation
     */
    const CORE = CorePlugin::CORE_LIBRARY;

    /**
     * The unique name for the special "widget" library
     * that represents the current widget when defining which events a widget definition may dispatch
     */
    const WIDGET = 'widget';

    /**
     * Fetches an event definition defined by this library
     *
     * @param string $eventName
     * @return EventDefinitionInterface
     * @throws EventDefinitionNotFoundException
     */
    public function getEventDefinitionByName($eventName);

    /**
     * Fetches a generic function defined by this library
     *
     * @param string $functionName
     * @return FunctionInterface
     * @throws FunctionNotSupportedException
     * @throws IncorrectFunctionTypeException
     */
    public function getGenericFunctionByName($functionName);

    /**
     * Fetches the unique name of this library
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches a signal definition defined by this library
     *
     * @param string $signalName
     * @return SignalDefinitionInterface
     * @throws SignalDefinitionNotFoundException
     */
    public function getSignalDefinitionByName($signalName);

    /**
     * Fetches an associative array of translation locales to translations
     *
     * @return array
     */
    public function getTranslations();

    /**
     * Fetches a widget definition defined by this library
     *
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     * @throws WidgetDefinitionNotSupportedException
     */
    public function getWidgetDefinitionByName($widgetDefinitionName);
}
