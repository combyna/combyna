<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html\WidgetRenderer\Gui;

use Combyna\Component\Renderer\Html\HtmlElement;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\RenderedWidgetInterface;

/**
 * Class BoxWidgetRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BoxWidgetRenderer implements WidgetRendererInterface
{
    /**
     * @var DelegatingWidgetRenderer
     */
    private $delegatingWidgetRenderer;

    /**
     * @param DelegatingWidgetRenderer $delegatingWidgetRenderer
     */
    public function __construct(DelegatingWidgetRenderer $delegatingWidgetRenderer)
    {
        $this->delegatingWidgetRenderer = $delegatingWidgetRenderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return 'gui';
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return 'box';
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget(RenderedWidgetInterface $renderedWidget)
    {
        $childNodes = $this->delegatingWidgetRenderer->renderWidget($renderedWidget->getChildWidget('contents'));
        $htmlAttributes = [];

        return new HtmlElement('div', $htmlAttributes, $childNodes);
    }
}
