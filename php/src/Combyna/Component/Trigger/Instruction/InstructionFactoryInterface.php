<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Instruction;

/**
 * Interface InstructionFactoryInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionFactoryInterface
{
    /**
     * Creates a new InstructionList
     *
     * @param InstructionInterface[] $instructions
     * @return InstructionListInterface
     */
    public function createInstructionList(array $instructions);
}
