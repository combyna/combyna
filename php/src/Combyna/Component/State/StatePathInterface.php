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

use Combyna\Component\State\Exception\AncestorStateUnavailableException;

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
     * Fetches the second-to-last state in the path
     *
     * @return StateInterface
     * @throws AncestorStateUnavailableException
     */
    public function getParentState();

    /**
     * Fetches the type of the second-to-last state in the path
     *
     * @return string
     * @throws AncestorStateUnavailableException
     */
    public function getParentStateType();

    /**
     * Fetches the states in the path in order, app first
     *
     * @return StateInterface[]
     */
    public function getStates();

    /**
     * Determines whether the end state in the path has a parent (ie. whether there is more than one state in the path)
     *
     * @return bool
     */
    public function hasParent();
}
