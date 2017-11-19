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

use Combyna\Component\Trigger\Config\Act\SignalInstructionNode;

/**
 * Interface SignalInstructionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SignalInstructionLoaderInterface
{
    /**
     * Creates a signal instruction node from a config array
     *
     * @param array $instructionConfig
     * @return SignalInstructionNode
     */
    public function load(array $instructionConfig);
}
