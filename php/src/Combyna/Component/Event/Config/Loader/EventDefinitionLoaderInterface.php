<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Event\Config\Loader;

use Combyna\Component\Event\Config\Act\EventDefinitionNode;

/**
 * Interface EventDefinitionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionLoaderInterface
{
    /**
     * Creates a event definition from a config array
     *
     * @param string $libraryName
     * @param string $eventName
     * @param array $eventDefinitionConfig
     * @return EventDefinitionNode
     */
    public function load(
        $libraryName,
        $eventName,
        array $eventDefinitionConfig
    );
}
