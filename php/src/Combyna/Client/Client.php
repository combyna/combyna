<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Client;

use Combyna\Component\App\AppInterface;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Framework\Combyna;
use Combyna\Component\Framework\EventDispatcher\Event\AppStateUpdatedEvent;
use Combyna\Component\Framework\FrameworkEvents;
use Combyna\Component\Renderer\Html\ArrayRenderer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Client.
 *
 * A facade for interacting with the app on the client side.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class Client
{
    /**
     * @var AppInterface
     */
    private $app;

    /**
     * @var ArrayRenderer
     */
    private $arrayRenderer;

    /**
     * @var Combyna
     */
    private $combyna;

    /**
     * @var AppStateInterface
     */
    private $currentAppState;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param Combyna $combyna
     * @param ArrayRenderer $arrayRenderer
     * @param AppInterface $app
     * @param AppStateInterface $initialAppState
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        Combyna $combyna,
        ArrayRenderer $arrayRenderer,
        AppInterface $app,
        AppStateInterface $initialAppState
    ) {
        $this->app = $app;
        $this->arrayRenderer = $arrayRenderer;
        $this->combyna = $combyna;
        $this->currentAppState = $initialAppState;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Dispatches an event for the app, storing the new state afterwards if something reacted.
     *
     * @param string[]|int[] $widgetStatePath
     * @param string $eventLibraryName
     * @param string $eventName
     * @param array $eventPayload
     * @throws NotFoundException
     */
    public function dispatchEvent(
        array $widgetStatePath,
        $eventLibraryName,
        $eventName,
        array $eventPayload
    ) {
        $newAppState = $this->app->dispatchEvent(
            $this->currentAppState,
            $this->currentAppState->getWidgetStatePathByPath($widgetStatePath),
            $eventLibraryName,
            $eventName,
            $eventPayload
        );

        $this->updateAppState($newAppState);
    }

    /**
     * Fetches the current state of the app.
     *
     * @return AppStateInterface
     */
    public function getCurrentAppState()
    {
        return $this->currentAppState;
    }

    /**
     * Fetches the arguments for the current route.
     *
     * @return StaticBagInterface
     */
    public function getCurrentRouteArgumentBag()
    {
        return $this->currentAppState->getProgramState()->getRouterState()->getRouteArgumentBag();
    }

    /**
     * Fetches the library of the current route.
     *
     * @return string
     */
    public function getCurrentRouteLibraryName()
    {
        return $this->currentAppState->getProgramState()->getRouterState()->getRoute()->getLibraryName();
    }

    /**
     * Fetches the name of the current route within its library.
     *
     * @return string
     */
    public function getCurrentRouteName()
    {
        return $this->currentAppState->getProgramState()->getRouterState()->getRoute()->getRouteName();
    }

    /**
     * Fetches the current URL of the app.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        $routerState = $this->currentAppState->getProgramState()->getRouterState();

        return $routerState->getRoute()->generateUrl($routerState->getRouteArgumentBag());
    }

    /**
     * Fetches the service container. This allows external code to inspect and modify
     * the service container (in debug mode) as needed.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->combyna->getContainer();
    }

    /**
     * Navigates to a new route.
     *
     * @param string $libraryName
     * @param string $routeName
     * @param array $routeArguments
     */
    public function navigateTo($libraryName, $routeName, array $routeArguments = [])
    {
        $newAppState = $this->app->navigateTo(
            $this->currentAppState,
            $libraryName,
            $routeName,
            $routeArguments
        );

        $this->updateAppState($newAppState);
    }

    /**
     * Adds a callback to be called when the app state changes.
     *
     * @param callable $callback
     */
    public function onAppStateUpdated(callable $callback)
    {
        $this->combyna->onAppStateUpdated($callback);
    }

    /**
     * Adds a callback to be called when any broadcast signal is dispatched.
     *
     * @param callable $callback
     */
    public function onBroadcastSignal(callable $callback)
    {
        $this->combyna->onBroadcastSignal($callback);
    }

    /**
     * Adds a callback to be called when any route is navigated to.
     *
     * @param callable $callback
     */
    public function onRouteNavigated(callable $callback)
    {
        $this->combyna->onRouteNavigated($callback);
    }

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
     */
    public function reevaluateUiState()
    {
        $newAppState = $this->app->reevaluateUiState($this->currentAppState);

        $this->updateAppState($newAppState);
    }

    /**
     * Renders all views that are visible in the provided app state to a plain array structure.
     *
     * @return array
     */
    public function renderVisibleViews()
    {
        return $this->arrayRenderer->renderViews($this->currentAppState, $this->app->getProgram());
    }

    /**
     * Stores the new app state, dispatching an event to be used for updating the UI.
     *
     * @param AppStateInterface $newAppState
     */
    public function updateAppState(AppStateInterface $newAppState)
    {
        $this->currentAppState = $newAppState;

        $this->eventDispatcher->dispatch(
            FrameworkEvents::APP_STATE_UPDATED,
            new AppStateUpdatedEvent($newAppState)
        );
    }
}
