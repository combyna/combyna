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

use Combyna\Component\Expression\StaticInterface;
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
     * Fetches the name to use for the variable that contains the index of the current item, if specified
     *
     * @return string|null
     */
    public function getIndexVariableName();

    /**
     * Fetches the evaluated static for the specified item in the list
     *
     * @param int $index
     * @return StaticInterface
     */
    public function getItemStatic($index);

    /**
     * Fetches the name to use for the variable that contains the value of the current item
     *
     * @return string
     */
    public function getItemVariableName();

    /**
     * Fetches the list of states for each repeated instance of the repeated widget
     *
     * @return WidgetStateInterface[]
     */
    public function getRepeatedWidgetStates();

    /**
     * Either creates a new widget state with the specified new sub-states
     * or just returns the current one, if it already has all of the same sub-states
     *
     * @param StaticInterface[] $itemStatics
     * @param WidgetStateInterface[] $repeatedWidgetStates
     * @return RepeaterWidgetStateInterface
     */
    public function with(array $itemStatics, array $repeatedWidgetStates);
}
