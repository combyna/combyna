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

/**
 * Interface WidgetGroupInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetGroupInterface extends CoreWidgetInterface
{
    /**
     * Adds a widget to this group
     *
     * @param WidgetInterface $childWidget
     */
    public function addChildWidget(WidgetInterface $childWidget);

    /**
     * Fetches the specified child widget of this one
     *
     * @param int $childIndex
     * @return WidgetInterface
     */
    public function getChildWidget($childIndex);

    /**
     * Fetches all child widgets of this one
     *
     * @return WidgetInterface[]
     */
    public function getChildWidgets();
}
