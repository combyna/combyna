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

use Combyna\Component\Bag\StaticBagInterface;
use Combyna\Component\Common\Exception\NotFoundException;

/**
 * Interface SignalRepositoryInterface
 *
 * Represents an event that has occurred or a request that has been made within the system
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalRepositoryInterface
{
    /**
     * Creates a new signal of the specified definition name and with the specified payload statics
     *
     * @param string $signalName
     * @param StaticBagInterface $payloadStaticBag
     * @return SignalInterface
     * @throws NotFoundException Throws when no definition has the specified name
     */
    public function createSignal($signalName, StaticBagInterface $payloadStaticBag);
}
