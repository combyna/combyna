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
 * Interface SignalDefinitionReferenceLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionReferenceLoaderInterface
{
    /**
     * Creates a signal definition references from a dot-separated name
     * in the format <library-name>.<signal-name>
     *
     * @param string $signalDefinitionReferenceName
     * @return SignalDefinitionReferenceNode
     */
    public function load($signalDefinitionReferenceName);

    /**
     * Creates a list of signal definition references from a list of dot-separated names
     * in the format <library-name>.<signal-name>
     *
     * @param string[] $signalDefinitionReferenceNames
     * @return SignalDefinitionReferenceNode[]
     */
    public function loadCollection(array $signalDefinitionReferenceNames);
}
