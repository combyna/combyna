<?php

/**
 * Combyna
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Entity\Instruction;

use Combyna\Component\Entity\EntityStorageInterface;
use Combyna\Component\Bag\StaticBagInterface;

/**
 * Interface EntityInstructionInterface
 *
 * Performs an operation on an entity
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface EntityInstructionInterface
{
    /**
     * Performs the operation that this instruction specifies
     *
     * @param StaticBagInterface $argumentStaticBag,
     * @param EntityStorageInterface $storage
     */
    public function perform(StaticBagInterface $argumentStaticBag, EntityStorageInterface $storage);
}
