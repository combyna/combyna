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

use Combyna\Component\Signal\Config\Act\SignalDefinitionNode;

/**
 * Interface SignalDefinitionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalDefinitionLoaderInterface
{
    /**
     * Creates a signal definition from a config array
     *
     * @param string $libraryName
     * @param string $signalName
     * @param array $signalDefinitionConfig
     * @return SignalDefinitionNode
     */
    public function load(
        $libraryName,
        $signalName,
        array $signalDefinitionConfig
    );
}
