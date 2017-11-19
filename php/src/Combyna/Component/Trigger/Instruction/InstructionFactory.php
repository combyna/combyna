<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Instruction;

/**
 * Class InstructionFactory
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InstructionFactory implements InstructionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createInstructionList(array $instructions)
    {
        return new InstructionList($instructions);
    }
}
