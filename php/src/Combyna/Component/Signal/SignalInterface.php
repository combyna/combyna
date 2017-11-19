<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal;

use Combyna\Component\Bag\StaticBagInterface;

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
     * Fetches the unique name for the library that defines the signal type within the system
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the unique name for the signal type within the system
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the static bag containing the signal payload
     *
     * @return StaticBagInterface
     */
    public function getPayloadStaticBag();
}
