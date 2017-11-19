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

use Combyna\Component\Renderer\Html\TextNode;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\State\Widget\TextWidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStateInterface;
use Combyna\Component\Ui\State\Widget\WidgetStatePathInterface;
use Combyna\Component\Ui\Widget\TextWidget;
use InvalidArgumentException;

/**
 * Class TextWidgetRenderer
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TextWidgetRenderer implements WidgetRendererInterface
{
    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return TextWidget::LIBRARY;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return TextWidget::DEFINITION;
    }

    /**
     * {@inheritdoc}
     */
    public function renderWidget(WidgetStateInterface $widgetState, WidgetStatePathInterface $widgetStatePath)
    {
        if (
            !$widgetState instanceof TextWidgetStateInterface ||
            $widgetStatePath->getWidgetDefinitionLibraryName() !== $this->getWidgetDefinitionLibraryName() ||
            $widgetStatePath->getWidgetDefinitionName() !== $this->getWidgetDefinitionName()
        ) {
            throw new InvalidArgumentException('Text widget renderer must receive a core.text widget');
        }

        return new TextNode($widgetState->getText());
    }
}
