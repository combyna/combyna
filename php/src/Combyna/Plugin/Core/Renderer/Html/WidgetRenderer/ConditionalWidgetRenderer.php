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
use Combyna\Component\Ui\State\Widget\ConditionalWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Widget\ConditionalWidget;
use InvalidArgumentException;

/**
 * Class ConditionalWidgetRenderer
 *
 * Takes the state of a present ConditionalWidget and renders it to a DOM node tree
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConditionalWidgetRenderer implements WidgetRendererInterface
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
        return ConditionalWidget::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return ConditionalWidget::DEFINITION;
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
            !$widgetState instanceof ConditionalWidgetStateInterface ||
            $widgetState->getWidgetDefinitionLibraryName() !== $this->getWidgetDefinitionLibraryName() ||
            $widgetState->getWidgetDefinitionName() !== $this->getWidgetDefinitionName()
        ) {
            throw new InvalidArgumentException('Conditional widget renderer must receive a core.conditional widget');
        }

        $childNodes = [];

        if ($widgetState->getConsequentWidgetState() !== null) {
            $consequentWidgetStatePath = $widgetStatePath->getChildStatePath(
                $widgetState->getConsequentWidgetState()->getStateName()
            );

            $childNodes[] = $this->delegatingWidgetRenderer->renderWidget($consequentWidgetStatePath, $program);
        } elseif ($widgetState->getAlternateWidgetState() !== null) {
            $alternateWidgetStatePath = $widgetStatePath->getChildStatePath(
                $widgetState->getAlternateWidgetState()->getStateName()
            );

            $childNodes[] = $this->delegatingWidgetRenderer->renderWidget($alternateWidgetStatePath, $program);
        }

        return new RenderedWidget(
            $widgetState,
            // If the condition evaluates to false and no alternate widget is specified,
            // then $childNodes will be empty, producing an empty fragment,
            // which will not render as anything at all (as expected).
            new DocumentFragment($childNodes)
        );
    }
}
