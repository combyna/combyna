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

use Combyna\Component\Common\DelegatorInterface;
use Combyna\Component\Renderer\Html\HtmlNodeInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use LogicException;

/**
 * Class DelegatingWidgetRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DelegatingWidgetRenderer implements DelegatorInterface
{
    /**
     * @var WidgetRendererInterface[][]
     */
    private $widgetRenderers = [];

    /**
     * Adds a renderer for a new type of core widget
     *
     * @param WidgetRendererInterface $renderer
     */
    public function addWidgetRenderer(WidgetRendererInterface $renderer)
    {
        $this->widgetRenderers
            [$renderer->getWidgetDefinitionLibraryName()]
            [$renderer->getWidgetDefinitionName()] = $renderer;
    }

    /**
     * Renders the specified widget state to a HTML node
     *
     * @param WidgetStatePathInterface $widgetStatePath
     * @return HtmlNodeInterface
     */
    public function renderWidget(WidgetStatePathInterface $widgetStatePath)
    {
        $libraryName = $widgetStatePath->getWidgetDefinitionLibraryName();
        $widgetDefinitionName = $widgetStatePath->getWidgetDefinitionName();

        if (!array_key_exists($libraryName, $this->widgetRenderers)) {
            throw new LogicException(
                'No renderer is registered for any widget definition of library "' . $libraryName . '"'
            );
        }

        if (!array_key_exists($widgetDefinitionName, $this->widgetRenderers[$libraryName])) {
            throw new LogicException(
                'No renderer is registered for widget "' . $widgetDefinitionName .
                '" of library "' . $libraryName . '"'
            );
        }

        /** @var WidgetStateInterface $widgetState */
        $widgetState = $widgetStatePath->getEndState();

        return $this->widgetRenderers[$libraryName][$widgetDefinitionName]->renderWidget(
            $widgetState,
            $widgetStatePath
        );
    }
}
