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
 * Interface SignalHandlerCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalHandlerCollectionInterface
{
    /**
     * Fetches a signal handler from this collection by its unique name
     *
     * @param string $signalName
     * @return SignalHandlerInterface
     * @throws SignalHandlerNotFoundException Throws when no handler has the specified library and name
     */
    public function getByName($signalName);
}
