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
interface WidgetGroupStateInterface extends CoreWidgetStateInterface
{
    const TYPE = WidgetGroupNode::TYPE;

    /**
     * Adds a widget state to this group
     *
     * @param string $childName
     * @param WidgetStateInterface $childWidgetState
     */
    public function addChild($childName, WidgetStateInterface $childWidgetState);

    /**
     * Fetches all child widget states of this one
     *
     * @return WidgetStateInterface[]
     */
    public function getChildren();

    /**
     * Fetches the specified child widget state of this one
     *
     * @param string $name
     * @return WidgetStateInterface
     */
    public function getChildState($name);
}
