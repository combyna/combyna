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
 * Class ArrayRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ArrayRenderer
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
     * Renders the specified rendered view to an array structure
     *
     * @param RenderedViewInterface $renderedView
     * @return array
     */
    public function renderView(RenderedViewInterface $renderedView)
    {
        $viewName = $renderedView->getViewName();

        $renderedRootWidget = $this->widgetRenderer->renderWidget($renderedView->getRootWidget());

        return [
            'view-name' => $viewName,
            'widget' => $renderedRootWidget->toArray()
        ];
    }
}
