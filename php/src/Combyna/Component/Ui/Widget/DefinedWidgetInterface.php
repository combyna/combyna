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
}
