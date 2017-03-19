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
 * Interface RenderedWidgetFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RenderedWidgetFactoryInterface
{
    /**
     * Creates a RenderedView
     *
     * @param ViewInterface $view
     * @param StaticBagInterface $attributeStaticBag
     * @param RenderedWidgetInterface $renderedRootWidget
     * @return RenderedViewInterface
     */
    public function createRenderedView(
        ViewInterface $view,
        StaticBagInterface $attributeStaticBag,
        RenderedWidgetInterface $renderedRootWidget
    );

    /**
     * Creates a RenderedWidget
     *
     * @param WidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param RenderedWidgetInterface[] $renderedChildWidgets
     * @return RenderedWidgetInterface
     */
    public function createRenderedWidget(
        WidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $renderedChildWidgets = []
    );
}
