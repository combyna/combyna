<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Instruction\Config\Loader;

use Combyna\Component\Instruction\Config\Act\InstructionNodeInterface;

/**
 * Interface InstructionLoaderInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionLoaderInterface
{
    /**
     * Creates an instruction ACT node from the specified config array
     *
     * @param array $instructionConfig
     * @return InstructionNodeInterface
     */
    public function load(array $instructionConfig);
}
