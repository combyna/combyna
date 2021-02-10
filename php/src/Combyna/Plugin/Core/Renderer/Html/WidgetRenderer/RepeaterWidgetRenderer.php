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

use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Renderer\Html\DocumentFragment;
use Combyna\Component\Renderer\Html\RenderedWidget;
use Combyna\Component\Renderer\Html\WidgetRenderer\DelegatingWidgetRenderer;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\RepeaterWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Widget\RepeaterWidget;
use InvalidArgumentException;

/**
 * Class RepeaterWidgetRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RepeaterWidgetRenderer implements WidgetRendererInterface
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
        return RepeaterWidget::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return RepeaterWidget::DEFINITION;
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget(
        WidgetStateInterface $widgetState,
        WidgetStatePathInterface $widgetStatePath,
        ProgramInterface $program
    ) {
        if (
            !$widgetState instanceof RepeaterWidgetStateInterface ||
            $widgetState->getWidgetDefinitionLibraryName() !== $this->getWidgetDefinitionLibraryName() ||
            $widgetState->getWidgetDefinitionName() !== $this->getWidgetDefinitionName()
        ) {
            throw new InvalidArgumentException('Repeater widget renderer must receive a core.repeater widget');
        }

        $childNodes = [];

        foreach ($widgetState->getRepeatedWidgetStates() as $repeatedWidgetState) {
            $repeatedWidgetStatePath = $widgetStatePath->getChildStatePath($repeatedWidgetState->getStateName());

            $childNodes[] = $this->delegatingWidgetRenderer->renderWidget($repeatedWidgetStatePath, $program);
        }

        return new RenderedWidget(
            $widgetState,
            new DocumentFragment($childNodes)
        );
    }
}
