<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html;

use Combyna\Component\App\AppInterface;
use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

/**
 * Class ArrayRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArrayRenderer
{
    /**
     * @var UiStateFactoryInterface
     */
    private $uiStateFactory;

    /**
     * @var DelegatingWidgetRenderer
     */
    private $widgetRenderer;

    /**
     * @param DelegatingWidgetRenderer $widgetRenderer
     * @param UiStateFactoryInterface $uiStateFactory
     */
    public function __construct(DelegatingWidgetRenderer $widgetRenderer, UiStateFactoryInterface $uiStateFactory)
    {
        $this->uiStateFactory = $uiStateFactory;
        $this->widgetRenderer = $widgetRenderer;
    }

    /**
     * Renders the specified app state's visible views to an array structure
     *
     * @param AppStateInterface $appState
     * @param AppInterface $app
     * @return array
     */
    public function renderViews(AppStateInterface $appState, AppInterface $app)
    {
        $viewsData = [];

        foreach ($appState->getVisibleViewStates() as $viewState) {
            $viewName = $viewState->getViewName();

            $rootWidgetStatePath = $this->uiStateFactory->createWidgetStatePath([
                $viewState,
                $viewState->getRootWidgetState()
            ]);
            $renderedRootWidget = $this->widgetRenderer->renderWidget($rootWidgetStatePath, $app);

            $viewsData[] = [
                'type' => $viewState->getType(),
                'view-name' => $viewName,
                'widget' => $renderedRootWidget->toArray()
            ];
        }

        return $viewsData;
    }
}
