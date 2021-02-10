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

use Combyna\Component\Ui\Config\Act\ConditionalWidgetNode;

/**
 * Interface ConditionalWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ConditionalWidgetStateInterface extends CoreWidgetStateInterface, ParentWidgetStateInterface
{
    const ALTERNATE_CHILD_NAME = 'alternate';
    const CONSEQUENT_CHILD_NAME = 'consequent';
    const TYPE = ConditionalWidgetNode::TYPE;

    /**
     * Fetches the state for the alternate widget, if it is set and present, or null if it does not
     * (based on the result of evaluating the condition expression)
     *
     * @return WidgetStateInterface|null
     */
    public function getAlternateWidgetState();

    /**
     * Fetches the state for the consequent widget, if it is present, or null if it does not
     * (based on the result of evaluating the condition expression)
     *
     * @return WidgetStateInterface|null
     */
    public function getConsequentWidgetState();

    /**
     * Either creates a new widget state with the specified new sub-states
     * or just returns the current one, if it already has all of the same sub-states
     *
     * @param WidgetStateInterface|null $consequentWidgetState
     * @param WidgetStateInterface|null $alternateWidgetState
     * @return ConditionalWidgetStateInterface
     */
    public function with(
        WidgetStateInterface $consequentWidgetState = null,
        WidgetStateInterface $alternateWidgetState = null
    );
}
