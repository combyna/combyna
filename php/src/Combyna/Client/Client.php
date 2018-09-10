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
use Combyna\Component\Common\Exception\NotFoundException;
use Combyna\Component\Renderer\Html\ArrayRenderer;

/**
 * Class Client
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
     * @param ArrayRenderer $arrayRenderer
     * @param AppInterface $app
     */
    public function __construct(ArrayRenderer $arrayRenderer, AppInterface $app)
    {
        $this->app = $app;
        $this->arrayRenderer = $arrayRenderer;
    }

    /**
     * Creates an initial state for the app
     *
     * @return AppStateInterface
     */
    public function createInitialState()
    {
        return $this->app->createInitialState();
    }

    /**
     * Dispatches an event for the app, returning the new state afterwards or the old state if nothing reacted
     *
     * @param AppStateInterface $oldAppState
     * @param string[]|int[] $widgetStatePath
     * @param string $eventLibraryName
     * @param string $eventName
     * @param array $eventPayload
     * @return AppStateInterface
     * @throws NotFoundException
     */
    public function dispatchEvent(
        AppStateInterface $oldAppState,
        array $widgetStatePath,
        $eventLibraryName,
        $eventName,
        array $eventPayload
    ) {
        $newAppState = $this->app->dispatchEvent(
            $oldAppState,
            $oldAppState->getWidgetStatePathByPath($widgetStatePath),
            $eventLibraryName,
            $eventName,
            $eventPayload
        );

        return $newAppState;
    }

    /**
     * Navigates to a new route
     *
     * @param AppStateInterface $appState
     * @param string $libraryName
     * @param string $routeName
     * @return AppStateInterface
     */
    public function navigateTo(AppStateInterface $appState, $libraryName, $routeName)
    {
        return $this->app->navigateTo($appState, $libraryName, $routeName);
    }

    /**
     * Renders all views that are visible in the provided app state to a plain array structure
     *
     * @param AppStateInterface $appState
     * @return array
     */
    public function renderVisibleViews(AppStateInterface $appState)
    {
        return $this->arrayRenderer->renderViews($appState);
    }
}
