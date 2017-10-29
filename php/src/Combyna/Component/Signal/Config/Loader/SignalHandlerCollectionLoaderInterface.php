<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Signal\Config\Loader;

use Combyna\Component\Signal\Config\Act\SignalHandlerNode;

/**
 * Interface SignalHandlerCollectionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalHandlerCollectionLoaderInterface
{
    /**
     * Creates a list of signal handler nodes from a config array
     *
     * @param array $signalHandlerConfigs
     * @return SignalHandlerNode[]
     */
    public function loadCollection(array $signalHandlerConfigs);
}
