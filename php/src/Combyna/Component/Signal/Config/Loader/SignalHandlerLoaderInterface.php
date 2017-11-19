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

use Combyna\Component\Signal\Config\Act\SignalHandlerNode;

/**
 * Interface SignalHandlerLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalHandlerLoaderInterface
{
    /**
     * Creates a signal handler from a config array
     *
     * @param string $signalLibraryName
     * @param string $signalName
     * @param array $signalHandlerConfig
     * @return SignalHandlerNode
     */
    public function load(
        $signalLibraryName,
        $signalName,
        array $signalHandlerConfig
    );
}
