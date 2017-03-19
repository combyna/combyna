<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html\WidgetRenderer;

use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Ui\RenderedWidgetInterface;

/**
 * Interface WidgetRendererInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetRendererInterface
{
    /**
     * Fetches the unique name of the library this renderer can render widgets using definitions of
     *
     * @return string
     */
    public function getWidgetDefinitionLibraryName();

    /**
     * Fetches the unique name of the widget definition this renderer can render widgets of
     *
     * @return string
     */
    public function getWidgetDefinitionName();

    /**
     * Renders the specified widget to a HTML node
     *
     * @param RenderedWidgetInterface $renderedWidget
     * @return HtmlNodeInterface
     */
    public function renderWidget(RenderedWidgetInterface $renderedWidget);
}
