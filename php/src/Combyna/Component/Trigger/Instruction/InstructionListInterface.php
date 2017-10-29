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
use Combyna\Component\Instruction\InstructionListInterface as BaseInstructionListInterface;
use Combyna\Component\Program\ProgramInterface;
use Combyna\Component\Program\State\ProgramStateInterface;

/**
 * Interface InstructionListInterface
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface InstructionListInterface extends BaseInstructionListInterface
{
    /**
     * {@inheritdoc}
     *
     * @return InstructionInterface[]
     */
    public function getInstructions();

    /**
     * Performs all instructions in this list in order, returning the final app state
     *
     * @param EvaluationContextInterface $evaluationContext
     * @param ProgramStateInterface $programState
     * @param ProgramInterface $program
     * @return ProgramStateInterface
     */
    public function performAll(
        EvaluationContextInterface $evaluationContext,
        ProgramStateInterface $programState,
        ProgramInterface $program
    );
}
