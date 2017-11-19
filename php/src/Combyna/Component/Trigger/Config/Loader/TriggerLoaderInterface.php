<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Loader;

use Combyna\Component\Trigger\Config\Act\TriggerNode;

/**
 * Interface TriggerLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface TriggerLoaderInterface
{
    /**
     * Creates a trigger node from a config array
     *
     * @param string $eventLibraryName
     * @param string $eventName
     * @param array $triggerConfig
     * @return TriggerNode
     */
    public function load(
        $eventLibraryName,
        $eventName,
        array $triggerConfig
    );

    /**
     * Creates a list of trigger nodes from a config array
     *
     * @param array $triggerConfigs
     * @return TriggerNode[]
     */
    public function loadCollection(array $triggerConfigs);
}
