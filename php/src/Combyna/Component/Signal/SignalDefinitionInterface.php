<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

/**
 * Interface SignalDefinitionInterface
 *
 * Defines the name and payload structure for an event that could occur
 * or a request that could be made within the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionInterface
{
    /**
     * Fetches the unique name for the signal type within the system
     *
     * @return string
     */
    public function getName();
}
