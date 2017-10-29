<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Loader;

use Combyna\Component\Ui\Store\Config\Act\SetViewStoreSlotInstructionNode;

/**
 * Interface SetViewStoreSlotInstructionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface SetViewStoreSlotInstructionLoaderInterface
{
    /**
     * Creates a signal instruction node from a config array
     *
     * @param array $instructionConfig
     * @return SetViewStoreSlotInstructionNode
     */
    public function load(array $instructionConfig);
}
