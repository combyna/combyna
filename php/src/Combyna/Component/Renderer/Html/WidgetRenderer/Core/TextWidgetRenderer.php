<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Renderer\Html\WidgetRenderer\Core;

use Combyna\Component\Renderer\Html\TextNode;
use Combyna\Component\Renderer\Html\WidgetRenderer\WidgetRendererInterface;
use Combyna\Component\Ui\RenderedWidgetInterface;
use Combyna\Component\Ui\TextWidget;

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
    public function renderWidget(RenderedWidgetInterface $renderedWidget)
    {
        return new TextNode($renderedWidget->getAttribute('text'));
    }
}
