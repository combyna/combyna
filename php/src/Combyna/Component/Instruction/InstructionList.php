<?php

/**
 * Combyna
 * Copyright (c) the Combyna project and contributors
 * https://github.com/combyna/combyna
 *
 * Released under the MIT license
 * https://github.com/combyna/combyna/raw/master/MIT-LICENSE.txt
 */

namespace Combyna\Component\Instruction;

/**
 * Class InstructionList
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class InstructionList implements InstructionListInterface
{
    /**
     * @var InstructionInterface[]
     */
    private $instructions;

    /**
     * @param InstructionInterface[] $instructions
     */
    public function __construct(array $instructions)
    {
        $this->instructions = $instructions;
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->instructions;
    }
}
