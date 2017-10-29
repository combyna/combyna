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

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;

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

    /**
     * {@inheritdoc}
     */
    public function performAll(
        EvaluationContextInterface $evaluationContext,
        ProgramStateInterface $programState,
        ProgramInterface $program
    ) {
        foreach ($this->instructions as $instruction) {
            $programState = $instruction->perform($evaluationContext, $programState, $program);
        }

        return $programState;
    }
}
