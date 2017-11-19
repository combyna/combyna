<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Loader;

use Combyna\Component\Signal\Config\Act\SignalDefinitionReferenceNode;

/**
 * Class SignalDefinitionReferenceLoader
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class SignalDefinitionReferenceLoader implements SignalDefinitionReferenceLoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function load($signalDefinitionReferenceName)
    {
        list($libraryName, $signalName) = explode('.', $signalDefinitionReferenceName);

        return new SignalDefinitionReferenceNode($libraryName, $signalName);
    }

    /**
     * {@inheritdoc}
     */
    public function loadCollection(array $signalDefinitionReferenceNames)
    {
        $signalDefinitionReferences = [];

        foreach ($signalDefinitionReferenceNames as $signalDefinitionReferenceName) {
            $signalDefinitionReferences[] = $this->load($signalDefinitionReferenceName);
        }

        return $signalDefinitionReferences;
    }
}
