<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Plugin\Gui\Renderer\Html\WidgetRenderer;

use Combyna\Component\Renderer\Html\HtmlElement;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use InvalidArgumentException;

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
    public function renderWidget(WidgetStateInterface $widgetState, WidgetStatePathInterface $widgetStatePath)
    {
        if (
            !$widgetState instanceof DefinedWidgetStateInterface ||
            $widgetState->getWidgetDefinitionLibraryName() !== $this->getWidgetDefinitionLibraryName() ||
            $widgetState->getWidgetDefinitionName() !== $this->getWidgetDefinitionName()
        ) {
            throw new InvalidArgumentException('Box widget renderer must receive a gui.box widget');
        }

        $childNode = $this->delegatingWidgetRenderer->renderWidget($widgetStatePath->getChildStatePath('contents'));
        $htmlAttributes = [];

        return new RenderedWidget(
            $widgetState,
            new HtmlElement('div', $widgetStatePath->getWidgetStatePath(), $htmlAttributes, [$childNode])
        );
    }
}
