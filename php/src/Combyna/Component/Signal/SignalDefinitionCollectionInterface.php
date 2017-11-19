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

/**
 * Interface SignalDefinitionCollectionInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionCollectionInterface
{
    /**
     * Fetches a signal definition from this collection by its unique name
     *
     * @param string $signalName
     * @return SignalDefinitionInterface
     * @throws SignalDefinitionNotFoundException Throws when no definition has the specified name
     */
    public function getByName($signalName);
}
