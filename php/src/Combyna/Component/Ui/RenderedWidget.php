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
 * Class RenderedWidget
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RenderedWidget implements RenderedWidgetInterface
{
    /**
     * @var StaticBagInterface
     */
    private $attributeStaticBag;

    /**
     * @var RenderedWidgetInterface[]
     */
    private $renderedChildWidgets;

    /**
     * @var WidgetInterface
     */
    private $widget;

    /**
     * @param WidgetInterface $widget
     * @param StaticBagInterface $attributeStaticBag
     * @param RenderedWidgetInterface[] $renderedChildWidgets
     */
    public function __construct(
        WidgetInterface $widget,
        StaticBagInterface $attributeStaticBag,
        array $renderedChildWidgets = []
    ) {
        $widget->assertValidAttributeStaticBag($attributeStaticBag);

        $this->attributeStaticBag = $attributeStaticBag;
        $this->renderedChildWidgets = $renderedChildWidgets;
        $this->widget = $widget;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name)
    {
        return $this->attributeStaticBag->getStatic($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getChildWidget($name)
    {
        return $this->renderedChildWidgets[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionLibraryName()
    {
        return $this->widget->getDefinitionLibraryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetDefinitionName()
    {
        return $this->widget->getDefinitionName();
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetPath()
    {
        return $this->widget->getPath();
    }
}
