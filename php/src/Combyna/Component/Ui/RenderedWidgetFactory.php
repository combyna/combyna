<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui;

use Combyna\Component\Bag\StaticBagInterface;

/**
 * Class RenderedWidgetFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RenderedWidgetFactory implements RenderedWidgetFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRenderedView(
        ViewInterface $view,
        StaticBagInterface $attributeStaticBag,
        RenderedWidgetInterface $renderedRootWidget
    ) {
        return new RenderedView($view, $attributeStaticBag, $renderedRootWidget);
    }

    /**
     * {@inheritdoc}
     */
    public function createRenderedWidget(
        WidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $renderedChildWidgets = []
    ) {
        return new RenderedWidget($widget, $attributeStaticBag, $renderedChildWidgets);
    }
}
