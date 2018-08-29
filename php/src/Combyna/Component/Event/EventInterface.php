<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

use Combyna\Component\Expression\StaticInterface;

/**
 * Interface EventInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventInterface
{
    /**
     * Fetches the unique name of the library that defines the event
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the unique name of the event within its library
     *
     * @return string
     */
    public function getName();

    /**
     * Fetches the specified static from this event's payload
     *
     * @param string $staticName
     * @return StaticInterface
     */
    public function getPayloadStatic($staticName);
}
