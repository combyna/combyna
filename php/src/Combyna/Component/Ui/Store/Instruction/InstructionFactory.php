<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Instruction;

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
    public function createViewStoreInstructionList(array $instructions)
    {
        return new ViewStoreInstructionList($instructions);
    }
}
