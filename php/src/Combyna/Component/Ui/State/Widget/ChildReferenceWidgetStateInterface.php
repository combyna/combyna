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

/**
 * Interface ChildReferenceWidgetStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ChildReferenceWidgetStateInterface extends CoreWidgetStateInterface, ParentWidgetStateInterface
{
    const TYPE = 'child-reference-widget';

    /**
     * Fetches the name of the child being referenced
     *
     * @return string
     */
    public function getChildName();

    /**
     * Either creates a new widget state with the specified new sub-states
     * or just returns the current one, if it already has all of the same sub-states
     *
     * @param WidgetStateInterface $referencedWidgetState
     * @return ChildReferenceWidgetStateInterface
     */
    public function with(WidgetStateInterface $referencedWidgetState);
}
