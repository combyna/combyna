<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
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
}
