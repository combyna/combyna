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
 * Interface RepeaterWidgetInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RepeaterWidgetInterface extends CoreWidgetInterface
{
    /**
     * Sets the widget to be repeated by this repeater
     *
     * @param WidgetInterface $repeatedWidget
     */
    public function setRepeatedWidget(WidgetInterface $repeatedWidget);
}
