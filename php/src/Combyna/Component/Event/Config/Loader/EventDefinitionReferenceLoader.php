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
 * Class EventDefinitionReferenceLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class EventDefinitionReferenceLoader implements EventDefinitionReferenceLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function loadCollection(array $eventDefinitionReferenceNames)
    {
        $eventDefinitionReferences = [];

        foreach ($eventDefinitionReferenceNames as $eventDefinitionReferenceName) {
            list($libraryName, $eventName) = explode('.', $eventDefinitionReferenceName);

            $eventDefinitionReferences[] = new EventDefinitionReferenceNode($libraryName, $eventName);
        }

        return $eventDefinitionReferences;
    }
}
