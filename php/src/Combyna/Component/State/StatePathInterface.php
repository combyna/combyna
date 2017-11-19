<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\State;

/**
 * Interface StatePathInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StatePathInterface
{
    /**
     * Fetches the final state in the path
     *
     * @return StateInterface
     */
    public function getEndState();

    /**
     * Fetches the type of the final state in the path
     *
     * @return string
     */
    public function getEndStateType();

    /**
     * Fetches the states in the path in order, app first
     *
     * @return StateInterface[]
     */
    public function getStates();
}
