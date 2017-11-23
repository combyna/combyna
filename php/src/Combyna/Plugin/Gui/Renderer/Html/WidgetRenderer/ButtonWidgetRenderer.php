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
use Combyna\Component\Renderer\Html\TextNode;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use InvalidArgumentException;

/**
 * Class ButtonWidgetRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ButtonWidgetRenderer implements WidgetRendererInterface
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
        return 'button';
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
            throw new InvalidArgumentException('Button widget renderer must receive a gui.button widget');
        }

        $childNodes = [
            new TextNode($widgetState->getAttribute('label')->toNative())
        ];
        $htmlAttributes = [];

        return new RenderedWidget(
            $widgetState,
            new HtmlElement('button', $widgetStatePath->getWidgetStatePath(), $htmlAttributes, $childNodes)
        );
    }
}
