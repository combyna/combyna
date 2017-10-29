<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Trigger\Config\Act;

use Combyna\Component\Program\ResourceRepositoryInterface;
use Combyna\Component\Trigger\Instruction\InstructionInterface;
use Combyna\Component\Trigger\Instruction\InstructionListInterface;

/**
 * Interface InstructionNodePromoterInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionNodePromoterInterface
{
    /**
     * Promotes an InstructionNode to an Instruction
     *
     * @param InstructionNodeInterface $instructionNode
     * @param ResourceRepositoryInterface $resourceRepository
     * @return InstructionInterface
     */
    public function promote(InstructionNodeInterface $instructionNode, ResourceRepositoryInterface $resourceRepository);

    /**
     * Promotes a list of trigger InstructionNodes to an InstructionList
     *
     * @param InstructionNodeInterface[] $instructionNodes
     * @param ResourceRepositoryInterface $resourceRepository
     * @return InstructionListInterface
     */
    public function promoteList(array $instructionNodes, ResourceRepositoryInterface $resourceRepository);
}
