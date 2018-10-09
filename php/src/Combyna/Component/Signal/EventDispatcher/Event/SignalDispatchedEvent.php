<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\EventDispatcher\Event;

use Combyna\Component\Signal\SignalInterface;

/**
 * Class SignalDispatchedEvent
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDispatchedEvent extends AbstractSignalEvent
{
    /**
     * @var SignalInterface
     */
    private $signal;

    /**
     * @param SignalInterface $signal
     */
    public function __construct(SignalInterface $signal)
    {
        $this->signal = $signal;
    }

    /**
     * Fetches the signal that was dispatched
     *
     * @return SignalInterface
     */
    public function getSignal()
    {
        return $this->signal;
    }
}
