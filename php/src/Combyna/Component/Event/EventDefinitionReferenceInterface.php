<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event;

/**
 * Interface EventDefinitionReferenceInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionReferenceInterface
{
    /**
     * Fetches the unique name of the event definition in its defining library
     *
     * @return string
     */
    public function getEventName();

    /**
     * Fetches the unique name of the library that defines the event definition
     *
     * @return string
     */
    public function getLibraryName();
}
