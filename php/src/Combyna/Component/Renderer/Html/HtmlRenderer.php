<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html;

use Combyna\Component\App\State\AppStateInterface;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Ui\State\UiStateFactoryInterface;

/**
 * Class HtmlRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HtmlRenderer
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
     * Renders the specified rendered app to HTML
     *
     * @param AppStateInterface $appState
     * @return string
     */
    public function renderApp(AppStateInterface $appState)
    {
        $viewsHtml = '';

        foreach ($appState->getVisibleViewStates() as $viewState) {
            $viewName = $viewState->getViewName();

            $encodedViewName = htmlentities($viewName);
            $rootWidgetStatePath = $this->uiStateFactory->createWidgetStatePath([
                $viewState,
                $viewState->getRootWidgetState()
            ]);
            $renderedRootWidget = $this->widgetRenderer->renderWidget($rootWidgetStatePath);
            $rootWidgetHtml = $renderedRootWidget->toHtml();

            $viewsHtml .= <<<HTML
<div class="combyna-view" data-view-name="$encodedViewName">
    $rootWidgetHtml
</div>
HTML;
        }

        return $viewsHtml;
    }
}
