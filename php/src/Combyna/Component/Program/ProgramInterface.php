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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Expression\Evaluation\RootEvaluationContext;
use Combyna\Component\Program\State\ProgramStateInterface;
use Combyna\Component\Router\State\RouterStateInterface;
use Combyna\Component\Signal\SignalDefinitionInterface;
use Combyna\Component\Signal\SignalInterface;
use Combyna\Component\Ui\View\PageViewInterface;
use Combyna\Component\Ui\Widget\WidgetDefinitionInterface;
use Combyna\Component\Ui\Widget\WidgetInterface;

/**
 * Interface ProgramInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ProgramInterface
{
    /**
     * Builds a URL for the specified route given the provided arguments
     *
     * @param string $libraryName
     * @param string $routeName
     * @param StaticBagInterface $argumentStaticBag
     * @return string
     */
    public function buildRouteUrl($libraryName, $routeName, StaticBagInterface $argumentStaticBag);

    /**
     * Creates an initial state for the program
     *
     * @param RouterStateInterface $routerState
     * @return ProgramStateInterface
     */
    public function createInitialState(RouterStateInterface $routerState);

    /**
     * Fetches a page view by its unique name
     *
     * @param string $name
     * @return PageViewInterface
     */
    public function getPageViewByName($name);

    /**
     * Fetches the root evaluation context for the app
     *
     * @return RootEvaluationContext
     */
    public function getRootEvaluationContext();

    /**
     * Fetches a signal definition with the given name from the current app or a library in the environment
     *
     * @param string $libraryName
     * @param string $signalName
     * @return SignalDefinitionInterface
     */
    public function getSignalDefinitionByName($libraryName, $signalName);

    /**
     * Fetches a widget (from any type of view) by its path
     *
     * @param string[]|int[] $names
     * @return WidgetInterface
     */
    public function getWidgetByPath(array $names);

    /**
     * Fetches a widget definition with the given name from a library in the environment
     *
     * @param string $libraryName
     * @param string $widgetDefinitionName
     * @return WidgetDefinitionInterface
     */
    public function getWidgetDefinitionByName($libraryName, $widgetDefinitionName);

    /**
     * Performs the actual internal handling of a dispatched signal
     *
     * @param ProgramStateInterface $programState
     * @param SignalInterface $signal
     * @return ProgramStateInterface
     */
    public function handleSignal(
        ProgramStateInterface $programState,
        SignalInterface $signal
    );

    /**
     * Navigates the app to a new location, using the specified route and its arguments
     *
     * @param ProgramStateInterface $programState
     * @param string $libraryName
     * @param string $routeName
     * @param StaticBagInterface $routeArgumentBag
     * @return ProgramStateInterface
     */
    public function navigateTo(
        ProgramStateInterface $programState,
        $libraryName,
        $routeName,
        StaticBagInterface $routeArgumentBag
    );

    /**
     * Re-evaluates the visible page view and any visible overlay views,
     * including the re-evaluation of any widget values by calling their respective
     * widget value providers. If no changes have occurred, the same immutable
     * ProgramState object will be returned.
     *
     * TODO: Consider only re-evaluating those parts of the UI whose expressions depend
     *       on a widget value. It would be complex to figure out exactly which widgets
     *       would need to be re-evaluated due to Captures, so for now we simply re-evaluate
     *       the entire visible UI on every input widget change or edit.
     *
     * @param ProgramStateInterface $programState
     * @return ProgramStateInterface
     */
    public function reevaluateUiState(ProgramStateInterface $programState);
}
