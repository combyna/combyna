<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\App;

use Combyna\Component\App\Exception\EventDispatchFailedException;
use Combyna\Component\App\Exception\SignalDispatchFailedException;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Expression\Evaluation\RootEvaluationContext;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface AppInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AppInterface
{
    /**
     * Creates a new AppState object, its internal state set up as specified in the app config
     *
     * @return AppStateInterface
     */
    public function createInitialState();

    /**
     * Dispatches an event to the application.
     *
     * This API is designed to be used from external code,
     * so only an event name and native payload values are required, the creation of the Event
     * object will be taken care of internally.
     *
     * The app state provided must be the same one the rendered widget belongs to,
     * otherwise an exception will be raised. This is because the app state dictates which widgets
     * are visible and which/how many exist - eg. from a Repeater - so if the app state changes
     * then the entire tree must be re-rendered
     *
     * @param AppStateInterface $appState
     * @param WidgetStatePathInterface $widgetStatePath
     * @param string $libraryName
     * @param string $eventName
     * @param array $payloadNatives
     * @return AppStateInterface
     * @throws EventDispatchFailedException
     */
    public function dispatchEvent(
        AppStateInterface $appState,
        WidgetStatePathInterface $widgetStatePath,
        $libraryName,
        $eventName,
        array $payloadNatives = []
    );

    /**
     * Dispatches a signal to the application.
     *
     * This API is designed to be used from external code,
     * so only a signal name and native payload values are required, the creation of the Signal
     * object will be taken care of internally
     *
     * @param AppStateInterface $appState
     * @param string $libraryName
     * @param string $signalName
     * @param array $payloadNatives
     * @return AppStateInterface
     * @throws SignalDispatchFailedException
     */
    public function dispatchSignal(AppStateInterface $appState, $libraryName, $signalName, array $payloadNatives);

    /**
     * Fetches a page view by its unique name
     *
     * @param string $name
     * @return PageViewInterface
     */
    public function getPageViewByName($name);

    /**
     * Fetches the internal representation of the app
     *
     * @return ProgramInterface
     */
    public function getProgram();

    /**
     * Fetches the root evaluation context for the app
     *
     * @return RootEvaluationContext
     */
    public function getRootEvaluationContext();

    /**
     * Fetches a widget (from any type of view) by its path
     *
     * @param string[]|int[] $names
     * @return WidgetInterface
     */
    public function getWidgetByPath(array $names);

    /**
     * Navigates to a new route
     *
     * This API is designed to be used from external code,
     * so only a route name and native argument values are required, the creation of the Signal
     * object will be taken care of internally
     *
     * @param AppStateInterface $appState
     * @param string $libraryName
     * @param string $routeName
     * @param array $routeArguments
     * @return AppStateInterface
     */
    public function navigateTo(AppStateInterface $appState, $libraryName, $routeName, array $routeArguments = []);

    /**
     * Re-evaluates the visible page view and any visible overlay views,
     * including the re-evaluation of any widget values by calling their respective
     * widget value providers. If no changes have occurred, the same immutable
     * AppState object will be returned.
     *
     * TODO: Consider only re-evaluating those parts of the UI whose expressions depend
     *       on a widget value. It would be complex to figure out exactly which widgets
     *       would need to be re-evaluated due to Captures, so for now we simply re-evaluate
     *       the entire visible UI on every input widget change or edit.
     *
     * @param AppStateInterface $appState
     * @return AppStateInterface
     */
    public function reevaluateUiState(AppStateInterface $appState);
}
