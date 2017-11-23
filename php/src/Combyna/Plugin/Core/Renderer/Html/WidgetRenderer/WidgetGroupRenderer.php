<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Plugin\Core\Renderer\Html\WidgetRenderer;

use Combyna\Component\Renderer\Html\DocumentFragment;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\WidgetGroupStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Widget\WidgetGroup;
use InvalidArgumentException;

/**
 * Class WidgetGroupRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class WidgetGroupRenderer implements WidgetRendererInterface
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
        return WidgetGroup::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return WidgetGroup::DEFINITION;
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget(WidgetStateInterface $widgetState, WidgetStatePathInterface $widgetStatePath)
    {
        if (
            !$widgetState instanceof WidgetGroupStateInterface ||
            $widgetState->getWidgetDefinitionLibraryName() !== $this->getWidgetDefinitionLibraryName() ||
            $widgetState->getWidgetDefinitionName() !== $this->getWidgetDefinitionName()
        ) {
            throw new InvalidArgumentException('Widget group renderer must receive a core.widget-group widget');
        }

        $childNodes = [];

        foreach ($widgetState->getChildren() as $childState) {
            $childStatePath = $widgetStatePath->getChildStatePath($childState->getStateName());

            $childNodes[] = $this->delegatingWidgetRenderer->renderWidget($childStatePath);
        }

        return new RenderedWidget(
            $widgetState,
            new DocumentFragment($childNodes)
        );
    }
}
