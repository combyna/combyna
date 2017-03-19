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

use Combyna\Component\Bag\FixedMutableStaticBagInterface;

/**
 * Interface SignalHandlerInterface
 *
 * Represents a service that can handle a dispatched signal
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalHandlerInterface
{
    /**
     * Handles a dispatched signal
     *
     * @param string $name Unique name for the signal type within the system
     * @param FixedMutableStaticBagInterface $payloadStaticBag Payload attached to the signal
     */
    public function handleSignal($name, FixedMutableStaticBagInterface $payloadStaticBag);
}
