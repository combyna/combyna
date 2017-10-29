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
 * Interface SignalDefinitionReferenceInterface
 *
 *
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionReferenceInterface
{
    /**
     * Fetches the unique name of the library that defines the signal definition
     *
     * @return string
     */
    public function getLibraryName();

    /**
     * Fetches the unique name for the signal type within the system
     *
     * @return string
     */
    public function getSignalName();
}
