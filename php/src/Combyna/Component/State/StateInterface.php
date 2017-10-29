<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\State;

/**
 * Interface StateInterface
 *
 * Represents the state of an object in the app, eg. a button widget or the app itself
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface StateInterface
{
    /**
     * Fetches the unique name for the state type within the system
     *
     * @return string
     */
    public function getType();
}
