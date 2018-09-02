<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State\Widget;

use Combyna\Component\Ui\Config\Act\RepeaterWidgetNode;

/**
 * Interface RepeaterWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface RepeaterWidgetStateInterface extends CoreWidgetStateInterface, ParentWidgetStateInterface
{
    const TYPE = RepeaterWidgetNode::TYPE;

    /**
     * Fetches the list of states for each repeated instance of the repeated widget
     *
     * @return WidgetStateInterface[]
     */
    public function getRepeatedWidgetStates();
}
