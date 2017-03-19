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
 * Interface SignalInterface
 *
 * Represents an event that has occurred or a request that has been made within the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalInterface
{
    /**
     * Fetches the unique name for the signal type within the system
     *
     * @return string
     */
    public function getName();

    /**
     * Dispatches this signal to the provided handler
     *
     * @param SignalHandlerInterface $signalHandler
     */
    public function dispatch(SignalHandlerInterface $signalHandler);
}
