<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Integrated\Ui\Fixtures;

use Combyna\Component\Renderer\Html\HtmlElement;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Renderer\Html\TextNode;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\DefinedWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use InvalidArgumentException;

/**
 * Class PokableButtonWidgetRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class PokableButtonWidgetRenderer implements WidgetRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return 'widget_values';
    }

    /**
     * Fetches the unique name of the widget definition this renderer can render widgets of
     *
     * @return string
     */
    public function getWidgetDefinitionName()
    {
        return 'pokable_button';
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
            throw new InvalidArgumentException('Pokable button widget renderer must receive a widget_values.pokable_button widget');
        }

        $childNodes = [
            new TextNode('My pokable button')
        ];
        $htmlAttributes = [];

        return new RenderedWidget(
            $widgetState,
            new HtmlElement('button', $widgetStatePath->getWidgetStatePath(), $htmlAttributes, $childNodes)
        );
    }
}
