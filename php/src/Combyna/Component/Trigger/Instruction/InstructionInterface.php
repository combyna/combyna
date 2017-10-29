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
use Combyna\Component\Instruction\InstructionInterface as BaseInstructionInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;

/**
 * Interface InstructionInterface
 *
 * Performs an operation given the current state, returning the new state
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionInterface extends BaseInstructionInterface
{
    /**
     * Performs the operation that this instruction specifies
     *
     * @param EvaluationContextInterface $evaluationContext
     * @param ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @return ProgramStateInterface
     */
    public function perform(
        EvaluationContextInterface $evaluationContext,
        ProgramStateInterface $programState,
        ProgramInterface $program
    );
}
