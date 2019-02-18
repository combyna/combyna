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
use Combyna\Component\Expression\StaticInterface;

/**
 * Interface SignalInterface
 *
 * Represents an action that has occurred or a request that has been made within the system
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
     * Fetches the specified static from this signal's payload
     *
     * @param string $staticName
     * @return StaticInterface
     */
    public function getPayloadStatic($staticName);

    /**
     * Fetches the entire payload for this signal
     *
     * @return StaticBagInterface
     */
    public function getPayloadStaticBag();
}
