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

use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Ui\RenderedViewInterface;

/**
 * Class HtmlRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class HtmlRenderer
{
    /**
     * @var DelegatingWidgetRenderer
     */
    private $widgetRenderer;

    /**
     * @param DelegatingWidgetRenderer $widgetRenderer
     */
    public function __construct(DelegatingWidgetRenderer $widgetRenderer)
    {
        $this->widgetRenderer = $widgetRenderer;
    }

    /**
     * Renders the specified rendered view to HTML
     *
     * @param RenderedViewInterface $renderedView
     * @return string
     */
    public function renderView(RenderedViewInterface $renderedView)
    {
        $viewName = $renderedView->getViewName();

        $encodedViewName = htmlentities($viewName);
        $renderedRootWidget = $this->widgetRenderer->renderWidget($renderedView->getRootWidget());
        $rootWidgetHtml = $renderedRootWidget->toHtml();

        return <<<HTML
<div class="combyna-view" data-view-name="$encodedViewName">
    $rootWidgetHtml
</div>
HTML;
    }
}
