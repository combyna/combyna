<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Widget;

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Trigger\TriggerCollectionInterface;

/**
 * Interface DefinedWidgetInterface
 *
 * Defined widgets are the generic type of widget defined by a "widget definition".
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface DefinedWidgetInterface extends WidgetInterface
{
    /**
     * Adds another widget as a child of this one
     *
     * @param WidgetInterface $childWidget
     */
    public function addChildWidget(WidgetInterface $childWidget);

    /**
     * Checks that the provided static bag is a valid set of attributes for this widget
     *
     * @param StaticBagInterface $attributeStaticBag
     */
    public function assertValidAttributeStaticBag(StaticBagInterface $attributeStaticBag);

    /**
     * Fetches the specified named child widget of this one
     *
     * @param string $childName
     * @return WidgetInterface
     */
    public function getChildWidget($childName);

    /**
     * Fetches all child widgets of this one
     *
     * @return WidgetInterface[]
     */
    public function getChildWidgets();

    /**
     * Fetches the widget's definition
     *
     * @return WidgetDefinitionInterface
     */
    public function getDefinition();

    /**
     * Fetches the widget's collection of triggers
     *
     * @return TriggerCollectionInterface
     */
    public function getTriggers();
}
