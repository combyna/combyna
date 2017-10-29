<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Instruction;

/**
 * Interface InstructionListInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionListInterface
{
    /**
     * Fetches all instructions in this list in order
     *
     * @return InstructionInterface[]
     */
    public function getInstructions();
}
