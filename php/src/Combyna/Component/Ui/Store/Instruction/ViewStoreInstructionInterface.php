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

use Combyna\Component\Expression\Evaluation\EvaluationContextInterface;
use Combyna\Component\Instruction\InstructionInterface as BaseInstructionInterface;
use Combyna\Component\Ui\State\Store\ViewStoreStateInterface;

/**
 * Interface ViewStoreInstructionInterface
 *
 * Performs an operation given the current state, returning the new state
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface ViewStoreInstructionInterface extends BaseInstructionInterface
{
    /**
     * Performs the operation that this instruction specifies
     *
     * @param EvaluationContextInterface $evaluationContext
     * @param ViewStoreStateInterface $viewStoreState
     * @return ViewStoreStateInterface
     */
    public function perform(
        EvaluationContextInterface $evaluationContext,
        ViewStoreStateInterface $viewStoreState
    );
}
