<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\State;

use Combyna\Component\State\StateInterface;

/**
 * Interface UiStateInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface UiStateInterface extends StateInterface
{
    /**
     * Fetches the unique name of this state within its parent.
     * For a named child widget's state, this would be the name of the widget (eg. "body"),
     * but for a generated child of a Repeater, this will be its zero-based index
     *
     * @return string
     */
    public function getStateName();
}
