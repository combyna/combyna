<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Ui\Store\Config\Act;

use Combyna\Component\Ui\Store\Instruction\ViewStoreInstructionInterface;
use Combyna\Component\Ui\Store\Instruction\ViewStoreInstructionListInterface;

/**
 * Interface ViewStoreInstructionNodePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreInstructionNodePromoterInterface
{
    /**
     * Promotes an InstructionNode to an Instruction
     *
     * @param ViewStoreInstructionNodeInterface $instructionNode
     * @return ViewStoreInstructionInterface
     */
    public function promote(ViewStoreInstructionNodeInterface $instructionNode);

    /**
     * Promotes a list of view store InstructionNodes to an InstructionList
     *
     * @param ViewStoreInstructionNodeInterface[] $instructionNodes
     * @return ViewStoreInstructionListInterface
     */
    public function promoteList(array $instructionNodes);
}
