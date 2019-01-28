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

use Combyna\Component\Ui\Config\Act\WidgetGroupNode;

/**
 * Interface WidgetGroupStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface WidgetGroupStateInterface extends CoreWidgetStateInterface, ParentWidgetStateInterface
{
    const TYPE = WidgetGroupNode::TYPE;

    /**
     * Fetches all child widget states of this one
     *
     * @return WidgetStateInterface[]
     */
    public function getChildren();

    /**
     * Either creates a new widget state with the specified new sub-states
     * or just returns the current one, if it already has all of the same sub-states
     *
     * @param WidgetStateInterface[] $childWidgetStates
     * @return WidgetGroupStateInterface
     */
    public function with(array $childWidgetStates);
}
