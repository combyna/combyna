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

use Combyna\Component\Event\Config\Act\EventDefinitionReferenceNode;

/**
 * Interface EventDefinitionReferenceLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EventDefinitionReferenceLoaderInterface
{
    /**
     * Creates a list of event definition references from a list of dot-separated names
     * in the format <library-name>.<event-name>
     *
     * @param string[] $eventDefinitionReferenceNames
     * @return EventDefinitionReferenceNode[]
     */
    public function loadCollection(array $eventDefinitionReferenceNames);
}
