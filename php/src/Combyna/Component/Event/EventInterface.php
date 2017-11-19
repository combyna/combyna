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
    public function getEventLibraryName();

    /**
     * Fetches the unique name of the event within its library
     *
     * @return string
     */
    public function getEventName();
}
